<?php
namespace App\Unicredit\Utils;

use App\Models\Investor;
use App\Models\LoanFund;
use App\Models\LoanRequest;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Exceptions\FundingFailedException;
use App\Unicredit\Exceptions\NoLoanFunderFoundException;

class AutomaticLoanFunder 

{

    protected $dbLogger;

    protected $loanRequest;

    protected $investors;

    protected $requestEmployerId;

    protected $singleInvestor;

    protected $doubleInvestors;

    protected $quaterInvestors;

    private $code;

    public function __construct(DatabaseLogger $dbLogger)
    {
       
        $this->dbLogger = $dbLogger;
        $this->code = config('unicredit.flow')['loan_fund'];
        $this->financeHandler = new FinanceHandler(new TransactionLogger);
    }


    public function fundRequest(LoanRequest $loanRequest)
    {
        $this->loanRequest = $loanRequest;
        $this->requestEmployerId = $loanRequest->employment->employer_id;
        
        $this->getInvestorsByEmployment();
        $this->getInvestorsByLoanTenor();
        
       
        if ($this->getSingleInvestorWhoCanFund()) {
            return $this->proceedWithFunding($this->singleInvestor);
        }

        
        if ($this->getDoubleInvestorWhoCanFund()) {
           return $this->proceedWithFunding($this->doubleInvestors);
           
        }
        
        if ($this->getQuaterInvestorWhoCanFund()) {
           return  $this->proceedWithFunding($this->quaterInvestors);
           
        }

        throw new NoLoanFunderFoundException('No funder meet the funding criterias for this loan');
        
    }

    protected function getInvestorsByEmployment()
    {
       
        $investors= Investor::where('auto_invest', true)->get();
        $filtered = $investors->filter(function($item, $index){

            $inArray = in_array($this->requestEmployerId,json_decode($item->employer_loan));
           return $inArray;
        });

        $this->investors = $filtered;
       
      
        if($this->investors->count() == 0){
            throw new NoLoanFunderFoundException('No funder found interested in this employer');
        }

    } 

    protected function getInvestorsByLoanTenor()
    {
        // if investor loan tenor is 3 that means 3 -  6 months 
        // if investor loan tenor is 6 that means 6 - 12 months
       
        $this->investors = ($this->loanRequest->duration >= 6) ? 
          
             $this->investors->filter(function($item,$index){
                return $item->loan_tenors == 6;
            }) : $this->investors->filter(function($item,$index){
                return $item->loan_tenors == 3;
            });
         
       if($this->investors->count() == 0){
        throw new NoLoanFunderFoundException('No funder found interested in this loan tenor');
       }
        
    }

    protected function getSingleInvestorWhoCanFund()
    {
        $investor = $this->investors->where('loan_fraction','=','100');

        if($investor->isNotEmpty()){
            $investor = $investor->random();
            if($investor && $investor->canPlaceInvestment($this->loanRequest->amount)){
                $this->singleInvestor = $investor;
               
                return true;
            }
        }
        
        return false;
    }


    protected function getDoubleInvestorWhoCanFund()
    {
        $investors = $this->investors->where('loan_fraction','=','50')->filter(function($item,$index){
            return $item->canPlaceInvestment($this->loanRequest->amount * 50/100);
        });
        
        if($investors->count() > 1){
            $investor = $investors->random(2);
        }
       
        if($investors && $investors->count() == 2){
            $this->doubleInvestors = $investors;
            return true;
        }
        
        return false;
    }


    protected function getQuaterInvestorWhoCanFund()
    {
        $investors = $this->investors->where('loan_fraction','=',25)->filter(function($item,$index){
            return $item->canPlaceInvestment($this->loanRequest->amount * 25/100);
        });

        if($investors->count() > 3){
            $investor = $investors->random(4);
        }

        if($investors && $investors->count() == 4){
            $this->quaterInvestors = $investors;
            return true;
        }
        return false;
    }

    protected function proceedWithFunding($payload)
    {
       
        
            if($payload instanceof \Illuminate\Support\Collection ){

            $percentage = 100 / $payload->count();

            try{
                DB::beginTransaction();

                $payload->map(function($user,$index) use ($percentage){
                    $this->executeFunding($user, $percentage);         
                });

                $this->logFundingSuccess();
                DB::commit();

            }catch(\Exception $e){
                
                DB::rollback();
                throw new FundingFailedException($e->getMessage());
            }
            
        }

        if($payload instanceof \App\Models\Investor){
          
            try{
               DB::beginTransaction();

                $user = $payload;
                $percentage  = 100;

                $this->executeFunding($user, $percentage);

                $this->logFundingSuccess();
                DB::commit();

            }catch(\Exception $e){

                DB::rollback();
                throw new FundingFailedException($e->getMessage());
            }
           
        }


    }

    protected function executeFunding($user,$percentage)
    {
    
        $amount = $this->loanRequest->amount * $percentage/100;
        $data = [
            'investor_id' => $user->id,
            'request_id' => $this->loanRequest->id,
            'percentage' => $percentage,
            'amount' => $amount
        ];

        $loanFund = LoanFund::create($data);
        $borrower = $this->loanRequest->user;
        
        // $this->financeHandler->handleDouble(
        //     $user, $borrower, $amount, $this->loanRequest, 'WTE', $this->code
        // );

        $this->financeHandler->handleSingle(
            $user, 'debit', $amount, $this->loanRequest, 'W', $this->code
        );

        // TODO: send notifications
        $percentageLeft = $this->loanRequest->percentage_left - $percentage;
            
        $update = ['percentage_left' => $percentageLeft];
        
        // Set loan request as accepted if fully funded
        if ($percentageLeft <= 0) {
            $update['status'] = 4;
        }
        
        $this->loanRequest->update($update);
       
    }


    protected function logFundingSuccess()
    {
        $this->dbLogger->log($this->loanRequest,[
            'message'=>'Automatic Funding Attempt for '.$this->loanRequest->reference,
            'status'=>1,
            'title'=>' Loan Request Funding Success',
            'description'=>'Automatic Funding Attempt for Request '.$this->loanRequest->reference.' was successful'
        ]);
        return true;
    }

   

}
?>