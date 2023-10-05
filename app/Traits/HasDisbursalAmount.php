<?php
namespace App\Traits;

trait HasDisbursalAmount {


    public function getDisbursalAmount()
    {
        if($this->upfront_interest) {
            return $this->calcUpfrontInterestDisbursalAmount();
        }
        $successFee = $this->success_fee;
        $fee = $successFee > 0 ? $successFee : (2.5 / 100) * $this->amount;
        $sum = 0; // summation of debts from previous loans
            
        // If loan is topping up get referenced loans plans
        if ($loan = $this->loanReference) {
            $sum += $loan->is_penalized ? $this->penalizedLoan() : $this->normalLoan();
        }

        return $this->amount - ($sum + $fee);
    }

    public function calcUpfrontInterestDisbursalAmount(){
        $sum = 0;
        if ($loan = $this->loanReference) {
    
            $sum += $loan->is_penalized ? $this->penalizedLoan() : $this->normalLoan();
        }
        
        $upfrontInterest = $this->investorUpfrontInterest->total_payment;
        
        $loanAmount = $this->amount;
        $disbursedLoan = $loanAmount - ($sum + $upfrontInterest);
        return $disbursedLoan;
    }
    
    /**
     * Calculates disbursal amount for normal loans
     *
     * @return double
     */
    public function normalLoan(){
        
        $loan = $this->loanReference;
        
        $walletBalance = $loan->repaymentPlans->where('status', 1)->last()->wallet_balance;
        
        $plans = $loan->repaymentPlans->where('status', 0);

        $emis = 0;

        foreach($plans as $plan ){
            $emis += $plan->total_amount;
        }
       
        $sum = $walletBalance > 0 ? $emis - $walletBalance : $emis + abs($walletBalance);
        // Over here we add the loan wallet balances
        $wallet = $loan->user->loan_wallet;
        if($wallet > 0) { $sum -= $wallet; }
        if($wallet < 0) { $sum += abs($wallet); }
        
        return $sum;
    }
    

    /**
     * Calclulates disbursal amount for loans in penalized mood
     *
     * @return double
     */
    public function penalizedLoan(){
        // Get penalty loan and remaining plans not penalized
        $loan = $this->loanReference;
        $walletBalance = $loan->repaymentPlans->where('status', 1)->last()->wallet_balance;
        $penaltyBalance = $loan->user->loan_wallet;
       
        $plans = $loan->repaymentPlans->where('status', 0);

        $emis = 0;
        foreach($plans as $plan) {
            $emis += $plan->total_amount;
        }
        // if the penalty balance is negative add it to the emis 
        // Else lets have just the emis
        $newAmount =  $penaltyBalance < 0 ? abs($penaltyBalance) + $emis : $emis;

        $sum = $walletBalance > 0 ? $newAmount - $walletBalance : $newAmount + abs($walletBalance);
        return $sum;
    }
}