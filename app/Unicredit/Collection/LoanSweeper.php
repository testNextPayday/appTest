<?php

namespace App\Unicredit\Collection;


use App\Models\Loan;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\BillingCard;
use App\Models\RepaymentPlan;

use Illuminate\Support\Collection;
use App\Remita\DDM\DebitOrderIssuer;

use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Collection\CardService;


class LoanSweeper {
    
    private $dbLogger;
    
    private $cardService;
    
    public function __construct()
    {
        $this->dbLogger = new DatabaseLogger();
        $this->cardService = new CardService(new Client, new DatabaseLogger);
    }
    
    /**
     * Sweeps loans
     * 
     * @params Collection $loans
     * 
     */
    public function sweep(Collection $loans)
    {
        //DESC
        foreach($loans as $loan) {
            $this->sweepSingle($loan);
        }
        
        return true;
    }

    public function sweepSingle(Loan $loan)
    {
        //1. Checks loan collection mode
        $collectionPlan = strtoupper($this->getCollectionMethod($loan->collection_plan));
       
        $method = "sweep{$collectionPlan}Loan";
        
        if (!method_exists($this, $method)) return false; 
        
        return $this->$method($loan);
    }

    private function getCollectionMethod($code)
    {
        $method = '';
        if(in_array($code,[0,100])) $method  = 'ddm'; // these are the code used to represent ddm remita at different times
        return $method;
    }
    

    public function sweepDDMLoan(Loan $loan)
    {
        // get due repayments 
        // issue debit orders for repayments if they've not been issued
        $duePayments = $loan->unfulfilled()
                            ->ddmRemita()
                            ->approved()
                            ->repaymentPlans()
                            ->due()
                            ->get();
        
        foreach($duePayments as $payment) {
            // Decide whether to make a card attempt or issue a debit order
            //if ($payment->canMakeCardAttempt())
                
              //  $this->makeCardAttempt($payment);
            
            //else
            
                $this->issueDebitOrder($payment);
        }
        
        return true;
    }
    
    
    public function issueDebitOrder(RepaymentPlan $plan)
    {
        $issuer = new DebitOrderIssuer();
        
        $responses = $issuer->issueInBits($plan);
                
        $this->dbLogger->log($plan, $responses, 'debit-order');
    
        if($responses->isASuccess()) {
        
            $plan->update([
                'order_issued' => true, 
                'rrr' => $responses->getRRR(), // we will be getting the first
                'transaction_ref' => $responses->getTransactionRef(),
                'requestId' => $responses->getRequestId(),
                'status_message' => $responses->getMessage()
            ]);
            
        } else {
            // if none succeded then we should clean the buffers
            $plan->clearBuffers();
            
        }

    }
    

    public function makeCardAttempt(RepaymentPlan $plan)
    {
        $response = $this->cardService->attempt($plan);
        
        if ($response['status']) $this->markPlanAsPaid($plan);
        
        return $response;
        
    }
    
    
    private function markPlanAsPaid(RepaymentPlan $plan, $paymentDate = null)
    {
        // Collection approved, Update plan as paid
        // also check if collection is complete and update the loan as fulfilled
        
        $plan->update([
            'status' => true, 
            'date_paid' => $paymentDate ?? now()->toDateString()
        ]);
        
        $loan = $plan->loan;
        
        if ($loan->repaymentPlans()->whereStatus(false)->count() < 1) {
            $loan->update(['status' => "2"]);
        }
        
        return true;
    }
}