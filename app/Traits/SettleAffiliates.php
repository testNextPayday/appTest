<?php
namespace App\Traits;

use App\Models\Loan;
use App\Models\User;
use App\Models\Settings;
use App\Models\RepaymentPlan;
use App\Helpers\FinanceHandler;

trait SettleAffiliates 

{

    public $affiliateUsers = [
        'App\Models\Affiliate',
        'App\Models\User'
    ];
    
    /**
     * Credit an affiliate based on a particular loan
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Loan $loan
     * @param  \App\Helpers\FinanceHelper $financeHandler
     * @return  bool
     */
    public function settleAffiliateOnLoan(
        User $user, 
        Loan $loan, 
        FinanceHandler $financeHandler
    )
    {
        $referrer = $loan->collector;

        if (!$referrer) {
            return false;
        }

        if (! in_array(get_class($referrer), $this->affiliateUsers)) {
            return false;
        }

    
        if ($referrer instanceof \App\Models\Affiliate) {
            // if ($referrer->settings('commission_method') != 'disbursement') {
            //     return false;
            // }
            // Here there's an affiliate and this is the users first loan

            if ($loan->loanRequest->affiliate_repayment_type) {
                return false;
            }
            $rate = $referrer->commission_rate;
        }

        if ($referrer instanceof \App\Models\User) {
            if ($loan->loanRequest->affiliate_repayment_type) {
                return false;
            }
            $rate = Settings::borrowerCommissionRate();
        }


        $amount = $loan->is_top_up ? $loan->disbursal_amount : $loan->amount;
        $duration = $loan->duration;
       
        $commission = ($rate/ 100) * $amount;
        
        $code = config('unicredit.flow')['affiliate_loan_commission'];
        $financeHandler->handleSingle(
            $referrer,
            'credit',
            $commission,
            $loan,
            'W',
            $code
        );

        // Settle affiliates supervisor
        $supervisor = $referrer->supervisor;
        $supCommissionRate = Settings::supervisorCommissionRate();


        // if supervisor exists and supervisor has a valid commission rate
        if ($supervisor && $supCommissionRate) {

            $amount = $loan->is_top_up ? $loan->disbursal_amount : $loan->amount;
            
            $duration = $loan->duration;
            $supCommission = ($supCommissionRate / 100) * $amount;
            $code = config('unicredit.flow')['supervisor_affiliate_commission'];
            $financeHandler->handleSingle(
                $supervisor,
                'credit',
                $supCommission,
                $loan,
                'W',
                $code
            );
        }
       

        return true;
    }


    
    /**
     * Here we settle an affiliate based on repayment Plan basis
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\RepaymentPlan $plan
     * @param  \App\Helpers\FinanceHandler $financeHandler
     * @return  bool
     */
    public function settleAffiliateOnPlan(
        User $user, 
        RepaymentPlan $plan, 
        FinanceHandler $financeHandler
    )
    {
       
        // sorry over here referrer is now the person booking the loan
        $loan = $plan->loan;

        //check if there is a collector set on loan
        if ($loan->noCollector()) {
            return false;
        }
        
        $referrer = $loan->collector;
        // if (!$referrer || !($referrer instanceof \App\Models\Affiliate)) {
        //     return false;
        // }

        // if the reffer collects commission on repayment
        // // if the settings is not repayment and loan is not restructured or commission paid exit
        // $commissionSettings = $referrer->settings('commission_method');
        // if ( ($commissionSettings != 'repayment') && (!$plan->loan->is_restructured || $loan->commissionPaid()) ) {
          
        //     return false;
        // }

        // if the settings is not repayment and loan is not restructured or commission paid exit
        //$commissionSettings = $referrer->settings('commission_method');
        //$repaymentCommissionMethod = $commissionSettings == 'repayment';
        // $loanNotRestructured = !$plan->loan->is_restructured;
        //$commissionhasBeenPaid = $loan->commissionPaid();
        
        
        // commission has been paid skip
            // if ($commissionhasBeenPaid) {
            //     return false;
            // }
        // if the affiliate is not using commission based on repayment and the employer is not repayment enabled skip too 
        if ($referrer instanceof \App\Models\Affiliate) {
            // if ($referrer->settings('commission_method') != 'disbursement') {
            //     return false;
            // }
            // Here there's an affiliate and this is the users first loan

            if (!$loan->loanRequest->affiliate_repayment_type) {
                return false;
            }
            $rate = $referrer->commission_rate;
        }

        if ($referrer instanceof \App\Models\User) {
            if (!$loan->loanRequest->affiliate_repayment_type) {
                return false;
            }
            $rate = Settings::borrowerCommissionRate();
        }

        // Here there's an affiliate and this is the users first loan
        $settings = Settings::affiliateRepaymentCommission();
        $duartion = $loan->duration;
        $commission = ($settings / 100) * $plan->emi;

        $code = config('unicredit.flow')['affiliate_loan_commission'];
        $financeHandler->handleSingle(
            $referrer,
            'credit',
            $commission,
            $loan,
            'W',
            $code
        );

         // Settle affiliates supervisor
         $supervisor = $referrer->supervisor;
         $supCommissionRate = Settings::supervisorCommissionRate();

         // if supervisor exists and supervisor has a valid commission rate
         if ($supervisor && $supCommissionRate) {
             $duartion = $loan->duration;
             $supCommission = ($supCommissionRate / 100) * $plan->emi;
             $code = config('unicredit.flow')['supervisor_affiliate_commission'];
             $financeHandler->handleSingle(
                $supervisor,
                'credit',
                $supCommission,
                $loan,
                'W',
                $code
            );
         }
       

        return true;
    }


    
    /**
     * Settles an Affiliate assigned to an Investors Funding
     *
     * @return void
     */
    public function settleAffiliateOnFunding(
        $investor, $amount, FinanceHandler $financeHandler
    )
    {
        
        $referrer = $investor->referrer;

        if ($referrer) {

            if ($referrer instanceof \App\Models\Investor) {
                
                $rate = Settings::InvestorFundingCommissionRate();

            }else if($referrer instanceof \App\Models\Staff){

                $rate = Settings::InvestorFundingCommissionRate();
                
            } else if ($referrer instanceof \App\Models\Affiliate) {

                $rate = $referrer->commission_rate_investor;

            } else {

                $rate = 0;
            }

            $commission = ($rate/100) * $amount;

            $code = config('unicredit.flow')['wallet_fund_commission'];
            $financeHandler->handleSingle(
                $referrer,
                'credit',
                $commission,
                $investor,
                'W',
                $code
            );


        }
    
    }



    public function settleAffiliateOnPromissoryNote(
        $affiliate, $note, $amount, $tenure, $financeHandler
    )
    {
        if ($affiliate) {

            $rate = Settings::InvestorPromissoryNoteCommissionRate();

            $commission = ($rate * $tenure / 12)/100 * $amount;

            $code = config('unicredit.flow')['promissory_note_commission'];
            $financeHandler->handleSingle($affiliate, 'credit', $commission, $note, 'W', $code);
        }
    }
}