<?php 
namespace App\Helpers;

use App\Models\User;
use App\Models\Settings;
use App\Models\Affiliate;
use App\Models\LoanRequest;
use App\Models\RepaymentPlan;
use App\Helpers\FinanceHandler;
use App\Models\LoanRequestLevel;
use App\Helpers\TransactionLogger;
use App\Services\LoanRequestUpgradeService;
use App\Notifications\Users\LoanDegradeNotification;

class PlanUnconfirm 

{
    
    /**
     * Carries out unconfirm steps on a repayment plan
     *
     * @param  mixed $plan
     * @return void
     */
    public static function unconfirm(RepaymentPlan $plan)
    {        
        self::updatePlanData($plan);
        
        if ($plan->paid_out) {

            self::reversePayment($plan);

            $plan->update(['paid_out'=>false]);

            //debit affiliate commission

            //get affiliate
            $affiliatePaymentType = $plan->loan->loanRequest->affiliate_repayment_type;
            if($affiliatePaymentType){
                $referrer = $plan->loan->collector;               
                if($referrer){
                    if ($referrer instanceof \App\Models\Affiliate) {
                        // Here there's an affiliate and this is the users first loan
                        $rate = Settings::affiliateRepaymentCommission();
                    }
            
                    if ($referrer instanceof \App\Models\User) {        
                        $rate = Settings::borrowerCommissionRate();
                    }
                        
                    $commission = ($rate/ 100) * $plan->emi;

                    $code = config('unicredit.flow')['affiliate_commission_rvsl'];
                    $loan = $plan->loan;

                    $financeHandler = new FinanceHandler(new TransactionLogger);

                    $financeHandler->handleSingle(
                        $referrer,
                        'debit',
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
                        $supCommission = ($supCommissionRate / 100) * $plan->emi;
                        $code = config('unicredit.flow')['supervisor_commission_rvsl'];
                        $financeHandler->handleSingle(
                            $supervisor,
                            'debit',
                            $supCommission,
                            $loan,
                            'W',
                            $code
                        );
                    }

                }
            }         
    
        }

        $loan = $plan->loan;
        if ($loan->status == '2') {

            $loan->update(['status'=>'1']);

            // get all the funds active again
            $loanRequest = $loan->loanRequest;
            foreach ($loanRequest->funds as $fund) {
                $fund->update(['status'=> '2']);
            }
        }
        if((new LoanRequestUpgradeService())->checkLoanRequiresDowngrade($loan)){
            (new LoanRequestUpgradeService())->downgradeUser($loan);
        }
        
    }

    
    /**
     * Update needed unconfirm details
     *
     * @param  mixed $plan
     * @return void
     */
    public static function updatePlanData($plan) 
    {
        $plan->update([
            'status' => false,
            'date_paid' => null,
            'collection_mode' => null,
            'paid_amount'=>null,
            'wallet_balance'=>null,
        ]);
    }
    
    
    /**
     * reverse payments if paid out
     *
     * @param  mixed $plan
     * @return void
     */
    public static function reversePayment($plan)
    {
        $repayments = $plan->repayments;

        foreach ($repayments as $payment) {
           
            $payment->reverse();
        }
    }
}