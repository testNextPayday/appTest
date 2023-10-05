<?php
namespace App\Traits;

trait Settlement {
// NOTE : This trait was added to enable loans perform settlements

    public function getAccruedAmountAttribute()
    {
        $cur_month = $this->created_at->diffInMonths(now());
        ($cur_month == 0) ? $cur_month = 1 : $cur_month;
        $repayments = $this->repaymentPlans->where('month_no','<=',$cur_month);
        $accrued_fees = $repayments->pluck('interest')->sum() + $repayments->pluck('management_fee')->sum();

            //$interests = $this->repaymentPlans->where('month_no','<=',$cur_month)->sum('interest');
            //$management_fee = $this->repaymentPlans->('month_no','<=',$cur_month)->sum('management_fee');
            //$accrued_fees = ($interest + $management_fee);
    
        
        
        return round($accrued_fees,2);
    }

    public function canSettle()
    {
        // can settle if he has not paid all
       
        $paidRepayments = $this->repaymentPlans->where('status',1)->count();
        $totalRepayments = $this->repaymentPlans->count();
        return $paidRepayments < $totalRepayments;
    }

    public function settlement()
    {
        return $this->hasOne('App\Models\Settlement');
    }

    public function getRepaymentWalletAttribute()
    {
         return  round(optional($this->repaymentPlans->where('status',1)->last())->wallet_balance,2);
    }

    public function getClosingValueAttribute()
    {
        // If user has a positive wallet add the value to the deductions made
        $paymentHub = $this->user->loan_wallet;
        if($paymentHub > 0) {
            return round($this->current_value - ($this->total_deductions + $paymentHub),2);
        }
        
       return round($this->current_value - $this->total_deductions,2);
    }

    public function getTotalDeductionsAttribute()
    {
      
        if($this->repayment_wallet < 0){

            return round($this->deductions - abs($this->repayment_wallet),2);
        }else{
            return round($this->deductions + abs($this->repayment_wallet),2);
        }
       
    }


    public function getCurrentValueAttribute()
    {
        return round($this->accrued_amount + $this->amount,2);
    }


    public function getDeductionsAttribute()
    {
        if($this->repaymentPlans->first()->is_new){
            // new loan
            return round($this->repaymentPlans->where('status',1)->sum('emi'),2);
        }else{
            // old loans pay emi and management fee
            $emi = $this->repaymentPlans->where('status',1)->sum('emi');
            $management_fee = $this->repaymentPlans->where('status',1)->sum('management_fee');
            return round($emi + $management_fee,2);
        }
       
    }

    public function getPenalChargeAttribute()
    {
        $penalty = 0;
        // check if loan is on penalty
        if ($this->is_penalized) {
            $penalty = $this->onPenalty();
        }
        elseif ($this->isMatured()) {
            
            $penalty =  $this->overMaturedPenalty();
        }
        else {
            $penalty = $this->computePenalCharge();
        }

        return round($penalty, 2);
    }

    protected function onPenalty()
    {
        $wallet = $this->user->loan_wallet;
        if ($wallet < 0) {
            return abs($wallet);
        }
        return 0;
    }


    protected function overMaturedPenalty()
    {
        $unpaidRepayments = $this->getUnpaidRepayments();
       
        return (10/100) * $unpaidRepayments;
    }
    
    
    /**
     * Settlement Total
     *
     * @return float
     */
    public function getSettlementTotalAttribute()
    {
        $loan_wallet = $this->user->loan_wallet;

        $total = $this->penal_charge + $this->closing_value;

        if ($loan_wallet < 0 && abs($loan_wallet) > 100 && !$this->is_penalized) {
            $total +=abs($loan_wallet);
        }
        
        return round($total, 2);
    }


    public function computePenalCharge()
    {
        $cur_month = $this->created_at->diffInMonths(now());
        
        if($cur_month <= 3) {
            $charge =  (10/100) * $this->amount;
        }elseif(($cur_month > 3) && ($cur_month <= 6)){
            $charge = (7.5/100) * $this->amount;
        }elseif(($cur_month > 6) && ($cur_month <= 9)){
            $charge = (5/100) * $this->amount;
        }elseif(($cur_month > 9) && ($cur_month <= 12)){
            $charge = 0;
        }else{
            $charge = 0;
        }
       return $charge;
    }

    public function calculateInvestorsCut()
    {
       
        $amount = $this->current_value - ($this->deductions + $this->calculateAccruedFee());
        return round($amount,2); 
    }

    public function calculateAccruedFee()
    {
       
        $cur_month = $this->created_at->diffInMonths(now());
        ($cur_month == 0) ? $cur_month = 1 : $cur_month;
        
        $allRepaymentsFee = $this->repaymentPlans->where('month_no','<=',$cur_month)->sum('management_fee');
        $paidRepaymentsFee = $this->repaymentPlans->where('status',1)->sum('management_fee');

        return round($allRepaymentsFee - $paidRepaymentsFee,2);
    }

    public function calculateAccruedInterest()
    {
       
        $cur_month = $this->created_at->diffInMonths(now());
        ($cur_month == 0) ? $cur_month = 1 : $cur_month;
        
        $allRepaymentsInterest = $this->repaymentPlans->where('month_no','<=',$cur_month)->sum('interest');
        $paidRepaymentsInterest = $this->repaymentPlans->where('status',1)->sum('interest');

        return round($allRepaymentsInterest - $paidRepaymentsInterest,2);
    }



    
}
