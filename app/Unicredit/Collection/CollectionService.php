<?php

namespace App\Unicredit\Collection;

use App\Models\Loan;

use App\Remita\DDM\MandateManager;
use App\Remita\DAS\LoanDisburser;
use App\Models\RepaymentPlan;
use App\Models\Settings;

use Carbon\Carbon;
use App\Unicredit\Collection\Utilities;
use App\Unicredit\Logs\DatabaseLogger;

use App\Traits\CollectionMethodHandlers;
use App\Helpers\Constants;


class CollectionService
{
    
    use CollectionMethodHandlers;
    
    protected $startDate;

    protected $endDate;

    public $hasDates = false;

    /**
     * Prepares a loan for collection
     * @param Loan $loan
     * @return array
     */
    public function prepare(Loan $loan, $code = null)
    {
        // Get loan collection methods
        $collectionMethods = json_decode($loan->collection_methods) ?? [];        
        $responses = [];
        
        // If a code is provided, handle single method                
        if ($code) {            
            $responses[$code] = $this->prepareSingle($loan, $code);
            return $this->buildResponse($responses);
        }

        // else handle for all
        foreach($collectionMethods as $method) {
            $responses[$method->code] = $this->prepareSingle($loan, $method->code);
                  
        }
        
        
        return $this->buildResponse($responses);
    }
    
    
    /**
     * Sets up a loan for a single method
     * 
     * @param Loan $loan
     * @param String $code
     * 
     * @return array|RemitaResponse;
     */
    public function prepareSingle(Loan $loan, $code)    
    {
        $handler = @$this->collectionHandlers[$code];
        
            
        if (!method_exists($this, $handler)) {
            // Make false
            return [
                'status' => false,
                'message' => 'Invalid collection method'
            ];
            
        }

        return $this->$handler($loan);
    }

    
    /**
     * Sets the start and end dates for the current collection
     *
     * @param  mixed $startDate
     * @param  mixed $endDate
     * @return void
     */
    public function setDates($startDate, $endDate):void
    {
        $this->startDate   = $startDate;
        $this->endDate = $endDate;
        $this->hasDates = true;
    }
    
     
    /**
     * Sets up collections for a loan
     * @param Loan $loan
     * @return bool
     * @deprecated Used with flat emi
     */
    public function setupRepayments(Loan $loan)
    {
        $borrower = $loan->user;
        
        //activate all loan funds
        $loanRequest = $loan->loanRequest;
        $loanRequest->funds()->update(['status' => 2]);
       
        $paymentData = $loan->paymentData();
        $interest = $paymentData['monthly_interest'];
        $principalPart = $paymentData['monthly_principal'];
        
        $monthNumber = 0;
        $principalBalance = $loan->amount;
        $duration = $loan->duration;
        $tenure = $duration;
        $managementFee = $loanRequest->fee($loan->amount);
       
        
        while($duration > 0) {
            
            $principalBalance = ceil($principalBalance - $principalPart);
            
            $startDate = Carbon::createFromFormat('d/m/Y', Utilities::getStartDate());            
            RepaymentPlan::create([
                'loan_id' => $loan->id, 
                'interest' => ($loanRequest->upfront_interest == 0) ? $interest : 0, 
                'principal' => $principalPart,
                'emi' => $loan->emi,
                'management_fee' => ($loanRequest->upfront_interest == 0) ? $managementFee : 0, 
                'balance' => $principalBalance, 
                'payday' => $startDate->addMonths($monthNumber)->format('Y-m-d'),
                'month_no' => ++$monthNumber, 
                'status' => false,               
            ]);
            $duration = $duration - 1;
        }
        
        return true;
    }
    
    public function setupArmotizedRepayments(Loan $loan)
    {
        $borrower = $loan->user;        
        //activate all loan funds
        $loanRequest = $loan->loanRequest;
        $loanRequest->funds()->update(['status' => 2]);       
        $monthNumber = 0;
        $monthly_payments = $loan->monthlyPayment();
        $begin_bal = $loan->amount;
        $managementFee = $loan->mgt_fee();
        $duration = $loan->duration;
        $emi = $loan->emi();
        $interest_rate = ($loan->interest_percentage)/100;
        
        while($duration > 0) {
            $begin_bal = isset($end_balance) ? $end_balance : $begin_bal;

            $interest =  round($interest_rate * $begin_bal,5);
            $principalPart = round($monthly_payments - $interest, 5);
            $end_balance = round($begin_bal - $principalPart,5);            
            $startDate = Carbon::createFromFormat('d/m/Y', Utilities::getStartDate());            
            RepaymentPlan::create([
                'loan_id' => $loan->id, 
                'interest' => ($loanRequest->upfront_interest) ? 0 : $interest, 
                'principal' => $principalPart,
                'emi' => $loanRequest->upfront_interest ? $principalPart : $emi,
                'management_fee' => ($loanRequest->upfront_interest) ? 0 : $managementFee, 
                'balance' => $end_balance, 
                'begin_balance'=>$begin_bal,
                'payments'=>$monthly_payments,
                'is_new'=>true,
                'payday' => $startDate->addMonths($monthNumber)->format('Y-m-d'),
                'month_no' => ++$monthNumber, 
                'status' => false
            ]);
            $duration = $duration - 1;
        }        
        return true;
    }
    
    
    /**
     * Checks the status of a mandate
     * @param Loan $loan
     * @return array
     */
    public function checkMandateStatus(Loan $loan)
    {
        $response = (new MandateManager())->checkMandateStatus($loan);
        
        (new DatabaseLogger())->log($loan, $response);
        
        return $response;
    }
    
    
    /**
     * Generates a simple response array from responses
     * 
     * @param array $responses
     * @return array
     */
    private function buildResponse(array $responses)
    {
        // Aggregate the responses
        // Assign the loan status to the lowest individual status
        // Return a response (False if there are no trues, True if mixes or all trues)
        // Reason if mixes or trues mean that at least one collection method has been set up
        $status = false; $messages = [];
        $methodNames = Constants::generateCollectionCodeMap();
        
        foreach($responses as $code => $response) {
            
            $message = '';
            if($code != 102){
                if ($response['status']) {
                    $status = true;
                    $message = $methodNames[$code] . " setup successful.";
                } else {
                    $message = $methodNames[$code] . " setup failed.";
                }
            }else{
                if($response){
                    $status = true;
                    $message = $methodNames[$code] . " setup successful.";
                }else{
                    $message = $methodNames[$code] . " setup failed.";
                }
            }
            
            
            array_push($messages, $message);
        }
        
        
        return [
            'status' => $status, 
            'message' => $status ? implode(", ", $messages) : 'Loan setup failed'
        ];
    }
}