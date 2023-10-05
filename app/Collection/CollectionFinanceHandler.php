<?php


namespace App\Collection;

use App\Models\RepaymentPlan;
use App\Collection\LoanWalletLogger;
use App\Collection\CollectionLogFactory;
use App\Traits\ReferenceNumberGenerator;


class CollectionFinanceHandler {
    
    use ReferenceNumberGenerator;
    
    protected $refPrefix = 'UC-LWT-';
    
    protected $logger;
    
    protected $reference;

    public $date;
        
    /**
     *Entity
     *
     * @var \App\Models\RepaymentPlan
     */
    protected $entity;
    
    protected $amount;
    
   
    
    public function __construct(LoanWalletLogger $logger)
    {
        $this->logger = $logger;
        // generate a reference for transaction
        $this->reference = $this->generateReference('App\Models\LoanWalletTransaction');
    }

    
    /**
     * Log Collection on Penalty and update user loan wallet for penalties
     *
     * @param  mixed $loan
     * @param  mixed $amount
     * @param  mixed $operation
     * @return void
     */
    public function logPenaltyCollection($loan, $amount, $desc, $operation) 
    {
        
        // Deduct the amount from the user loan wallet
        $user = $loan->user;
        $loanWallet = $user->loan_wallet;

        if ($operation == 'debit') {
            $user->update(['loan_wallet'=> $loanWallet - $amount]);
            $direction = 2;
        }else {
            $user->update(['loan_wallet'=> $loanWallet + $amount]);
            $direction = 1;
        }

        $data = CollectionLogFactory::getPenaltyDataLog($loan, $amount, $desc, $direction, $this->reference, $this->date);
        $this->logger->log($data);
        
    }


    public function logCollection($loan, $amount, $desc, $operation, $method=null)
    {
        // Deduct the amount from the user loan wallet
        $user = $loan->user;
        $loanWallet = $user->loan_wallet;

        if ($operation == 'debit') {
            $user->update(['loan_wallet'=> $loanWallet - $amount]);
            $direction = 2;
        }else {
            $user->update(['loan_wallet'=> $loanWallet + $amount]);
            $direction = 1;
        }

        $data = CollectionLogFactory::getCollectionLogData($loan, $amount, $desc, $direction, $this->reference, $this->date, $method);
        $this->logger->log($data);
    } 

   
    /**
     * Approve Transaction By sending money to loan wallet
     *
     * @param  mixed $trnx
     * @return void
     */
    public function approveTrnx($trnx)
    {
       
        $trnx->update(['status'=> 2, 'is_logged'=> false, 'confirmed'=>true]);
        
        //Add transaction to user loan wallet
        $user = $trnx->user;
        
        $wallet = $user->loan_wallet + $trnx->amount;
       
        $user->update(['loan_wallet'=> $wallet]);

       
    }

    public function moveTopupCash($loan)
    {
        // take out cash from the wallet
        $user = $loan->user;
        $this->amount = $loan->getTopupDeficit();
        $loanWallet = $user->fresh()->loan_wallet - $this->amount;
        $user->fresh()->update(['loan_wallet'=> 0]);
        $logData = CollectionLogFactory::getSettlementOrTopupLogData($loan, $collectMode = 'Set-off',$this->reference, $this->amount, $loan);
        $this->logger->log($logData);
        $unpaid = $loan->loanReference->repaymentPlans->where('status', 0);
        foreach($unpaid as $plan) {
            $plan->update([
                'paid_amount'=> $plan->total_amount,
                'status'=> 1,
                'collection_mode'=> 'Set-off',
                'date_paid'=> now(),
                'paid_out'=> true,
                'wallet_balance'=> 0.00
            ]);
        }
    }
    
    /**
     * Takes cash from the wallet and updates all loan plans left open
     *
     * @param  mixed $loan
     * @param  mixed $settlement
     * @return void
     */
    public function moveSettlementCash($loan, $settlement)
    {
      
        // take out cash from the wallet
        $user = $loan->user;
        $this->amount = $settlement->amount;
        $loanWallet = $user->fresh()->loan_wallet - $settlement->amount;
        $user->fresh()->update(['loan_wallet'=> $loanWallet]);
        $logData = CollectionLogFactory::getSettlementOrTopupLogData($loan, $collectMode = 'Settlement',$this->reference, $this->amount, $settlement);
        $this->logger->log($logData);
        $unpaid = $loan->repaymentPlans->where('status', 0);
        foreach($unpaid as $plan) {
            $plan->update([
                'paid_amount'=> $plan->total_amount,
                'status'=> 1,
                'collection_mode'=> 'Settled',
                'date_paid'=> now(),
                'paid_out'=> true
            ]);
        }
    }
    
        
    /**
     * Logs movement from loan wallet to plan
     *
     * @param  \App\Models\RepaymentPlan $plan
     * @param  \App\Models\User $user
     * @return void
     */
    public function moveCashToPlan($plan, $user, $trnx=null)
    {
        
        $this->entity = $plan;
        $this->amount = $plan->total_amount;
        
        if ($this->checkConfirm($plan, $this->amount)){
            // Remove cash from wallet
            $loanWallet = $user->loan_wallet - $plan->total_amount;
            $user->update(['loan_wallet'=> $loanWallet]);

            $collectMethod = $trnx ? $trnx->collection_method : 'Flow from wallet';
           
            // Add cash to loan
            $plan->update([
                'paid_amount'=> $plan->total_amount,
                'status'=> 1,
                'collection_mode'=> session()->get('payment_method') ? session()->get('payment_method') :  $collectMethod,
                'date_paid'=> $trnx ? $trnx->collection_date : now()
            ]);
            $loan = $plan->loan;
            if ($loan->is_penalized && $plan->is_penalized) {
                // If loan and plan are penalized decrease the total_penable_debts
                // Because borrower has made payment
               
                $totalDebts = $loan->total_penable_debts - $plan->total_amount;
                $loan->update(['total_penable_debts'=> $totalDebts]);
            }

            session()->forget('payment_method');
            $direction = 2;

            $desc = 'Flow from wallet to plan';
            
            $logData = CollectionLogFactory::getLogData($user, $direction, $desc, $this->entity, $this->reference, $this->amount);
           
            $this->logger->log($logData);
                
            return true;
        }
        
        return false;
    }


    /**
     * Logs movement from plan to wallet
     *
     * @param  \App\Models\RepaymentPlan $plan
     * @param  \App\Models\User $user
     * @return void
     */
    public function moveCashToWallet($plan, $user)
    {
        
        $this->entity = $plan;
        $this->amount = $plan->total_amount;
        
        if ($plan->status == 1){
            // Remove cash from wallet
            $loanWallet = $user->loan_wallet + $plan->total_amount;
            $user->update(['loan_wallet'=> $loanWallet]);
           
            // Add cash to loan
            $plan->update([
                'paid_amount'=> null,
                'status'=> 0,
                'collection_mode'=> null,
                'date_paid'=> null,
                'unconfirmed_at'=> now()
            ]);

            if ($loan->is_penalized && $plan->is_penalized) {
                // If loan and plan are penalized increase the total_penable_debts
                // Because borrower cancelled payment
                $loan = $plan->loan;
                $totalDebts = $loan->total_penable_debts + $plan->total_amount;
                $loan->update(['total_penable_debts'=> $totalDebts]);
            }

            $direction = 1;

            $desc = 'Backward flow to wallet';
            
            $logData = CollectionLogFactory::getLogData($user, $direction, $desc, $this->entity, $this->reference, $this->amount);
           
            $this->logger->log($logData);
           
            return true;
        }
        
        return false;
    }

    
    /**
     * Can Confirm Plan
     *
     * @param  \App\Models\RepaymentPlan $plan
     * @param  double $amount
     * @return void
     */
    public function checkConfirm(RepaymentPlan $plan, $amount)
    {
        return $amount + 50 >= $plan->total_amount;
    }
   

    
    
}