<?php

namespace App\Traits;
use App\Models\RepaymentPlan;
trait InvestorAccounts
{
    /**
     * Interest portion of recoveries made
     * 
     */
    public function incomeEarned()
    {
        $incomeEarned = 0;

        // where not reversed
        $repayments = $this->recievedRepayments();

        foreach ($repayments as $repayment) {
            $plan = $repayment->plan;
            //$plan = RepaymentPlan::find($repayment['plan_id']);
            $extras = $repayment->commission + ($repayment->tax ?? 0);
            $interestAfterCommission = optional($plan)->interest - $extras;
            $incomeEarned += $interestAfterCommission;
        }

        return $incomeEarned;
    }

    
    /**
     * Total Vaule of loans sold
     *
     * @return void
     */
    public function auctionedLoans() 
    {
        return $soldLoans = $this->loanFunds()->whereStatus(5)->sum('sale_amount');
    }

    /**
     * Based on Interest portion of recoveries made
     * 
     */
    public function commissionsPaid()
    {
        $commissions = $this->recievedRepayments()->sum('commission');
        $pmf = $this->transactions->where('code', '027')->sum('amount');

        return ($pmf + $commissions);
    }


    public function taxPaid()
    {
        return $this->recievedRepayments()->sum('tax');
    }

    public function recoveriesMade()
    {
        $fundsGotten = $this->recievedRepayments()->sum('amount');
        return $fundsGotten;
    }

    public function undueReturns_Z()
    {
        $undueReturns['principal'] = 0;
        $undueReturns['interest'] = 0;
        $undueReturns['total'] = 0;

        //get given/acquired loan funds that have not been sold
        $givenFunds = $this->loanFunds()->with('loanRequest.loan.repaymentPlans')->whereIn('status', [2, 4])->get();

        foreach ($givenFunds as $fund) {
            //get corresponding loans
            $loan = $fund->loanRequest->loan;

            //look up repayment plans for these loans and get the number of unpaid/undue repayments
            //sum the principal with the interest

            //Get plan sample
            if (!$loan) continue;
            $plans = $loan->repaymentPlans->where('paid_out', 0);
            foreach ($plans as $plan) {

                if ($plan) {
                    //$unpaidCount = $loan->repaymentPlans()->whereStatus(false)->count();
                    $fundFraction = $fund->amount / $loan->amount;


                    $investorPrincipal = $plan->principal * $fundFraction;

                    $investorInterest = $plan->interest * $fundFraction;

                    $unpaidTotal = ($investorPrincipal + $investorInterest);

                    //add up 
                    $undueReturns['total'] += $unpaidTotal;
                    $undueReturns['interest'] += ($investorInterest);
                    $undueReturns['principal'] += ($investorPrincipal);
                }
            }
        }


        //TODO: Add pending funds to outstanding principal
        $pending = $this->loanFunds()->whereStatus(1)->sum('amount');
        $undueReturns['principal'] += $pending;

        return $undueReturns;
    }

    public function undueReturns()
    {
        $undueReturns['principal'] = 0;
        $undueReturns['interest'] = 0;
        $undueReturns['total'] = 0;
        $undueReturns['payments'] = [];

        //get given/acquired loan funds that have not been sold
        $givenFunds = $this->loanFunds()->with('loanRequest.loan.repaymentPlans')->whereIn('status', [2, 4])->get();

        foreach ($givenFunds as $fund) {
            //get corresponding loans
            $loan = $fund->loanRequest->loan;

            //look up repayment plans for these loans and get the number of unpaid/undue repayments
            //sum the principal with the interest

            //Get plan sample
            if (!$loan) continue;
            $plan = $loan->repaymentPlans()->first();

            if ($plan) {
                $unpaidCount = $loan->repaymentPlans()->whereStatus(false)->count();
                $fundFraction = $fund->amount / $loan->amount;


                $investorPrincipal = $plan->principal * $fundFraction;

                $investorInterest = $plan->interest * $fundFraction;

                $unpaidTotal = ($investorPrincipal + $investorInterest) * $unpaidCount;

                //add up 
                $undueReturns['total'] += $unpaidTotal;
                $undueReturns['interest'] += ($investorInterest * $unpaidCount);
                $undueReturns['principal'] += ($investorPrincipal * $unpaidCount);
            }
        }


        //TODO: Add pending funds to outstanding principal
        $pending = $this->loanFunds()->whereStatus(1)->sum('amount');
        $undueReturns['principal'] += $pending;

        return $undueReturns;
    }

    public function outstandingInterest()
    {
        return $this->undueReturns_Z()['interest'];
    }

    public function outstandingPrincipal()
    {
       
        return $this->undueReturns_Z()['principal'];
    }

    /** 
     * Portfolio = outstanding principal and interest on loans given
     */
    public function portfolioSize()
    {
        //Loans funds that are pending, active or on transfer
        // $funds = $this->loanFunds()->whereIn('status', [1, 2, 4])->get();

        // $fundsValue = $funds->sum('amount');

        // $interest = 0;

        // foreach($funds as $fund) {
        //     if ($fund->status != 1 && $loan = $fund->loanRequest->loan) {
        //         //Get plan sample
        //         $plan = $loan->repaymentPlans()->first();
        //         $unpaidCount = $loan->repaymentPlans()->whereStatus(false)->count();
        //         $fundFraction = $fund->amount / $loan->amount;
        //         $investorInterest = $plan->interest * $fundFraction;
        //         $interest += ($investorInterest * $unpaidCount);
        //     }
        // }

        return $this->outstandingPrincipal() +
            $this->outstandingInterest() +
            $this->wallet +
            $this->vault;
    }


   

    public function balanceOnLoans()
    {
        return $this->outstandingPrincipal();
    }

    public function balanceOnInterests()
    {
        return $this->outstandingInterest();
    }

    public function fundedLoans()
    {
        return $this->loanFunds->count();
    }

    public function presentValue()
    {
        return round($this->portfolioSize() - $this->outstandingInterest(), 2);
    }

    public function repaymentPlanCollection()
    {
       
       $plans = collect();

       $givenFunds = $this->loanFunds()->with('loanRequest.loan.repaymentPlans')->whereIn('status', [2, 4])->get();

      

       foreach ($givenFunds as $fund){

           $loan = optional($fund->loanRequest)->loan;
           
           if(!$loan) continue;
           
           if($loan->isActive()){

           

                $plans  =  $plans->merge(
                    $loan->repaymentPlans->where('status', true)->where('paid_out', false)
                );
            
           }
       }

      
      
       return $plans;
       
       
        
       
    }
}
