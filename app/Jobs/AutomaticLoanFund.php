<?php

namespace App\Jobs;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;

use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Unicredit\Utils\AutomaticLoanFunder;

class AutomaticLoanFund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $loanRequest;

    protected $dbLogger;

    /**
     * 5mins
     *  
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LoanRequest $loanRequest)
    {
        //
        $this->loanRequest  = $loanRequest;
        $this->dbLogger  =  new DatabaseLogger();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AutomaticLoanFunder $funder)
    {
        //
            try {

                $funder->fundRequest($this->loanRequest);
                
                session()->flash('info',' Request was successfully Funded');
            }catch(\Exception $e){

                session()->flash('info',$e->getMessage());

                if($e instanceof \App\Unicredit\Utils\FundingFailedException){
                    $this->logFundingFailed();
                }
                
                if($e instanceof \App\Unicredit\Utils\NoLoanFunderFoundException){
                    $this->logNoInvestorFound();
                }

                
            }
           
       
        
       
    }


    protected function logFundingFailed()
    {
        $this->dbLogger->log($this->loanRequest,[
            'message'=>'Automatic Funding Attempt for '.$this->loanRequest->reference,
            'status'=>0,
            'title'=>' Loan Request Funding Failed',
            'description'=>'Automatic Funding Attempt for Request '.$this->loanRequest->reference.' failed',
            
        ]);
        return false;

    }

    protected function logNoInvestorFound()
    {
        $this->dbLogger->log($this->loanRequest,[
            'message'=>'Automatic Funding Attempt for '.$this->loanRequest->reference.' Found No Investor',
            'status'=>0,
            'title'=>' Loan Request Funding Failed',
            'description'=>'Automatic Funding Attempt for Request '.$this->loanRequest->reference.' found no suitable investor',
            
        ]);
        return false;
    }
}
