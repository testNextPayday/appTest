<?php

namespace App\Observers;

use App\Models\RepaymentPlan;
use App\Helpers\FinanceHandler;
use App\Traits\SettleAffiliates;
use App\Helpers\TransactionLogger;

class RepaymentPlanObserver
{

    use SettleAffiliates;
    
    /**
     * Handle the repayment plan "created" event.
     *
     * @param  \App\Models\RepaymentPlan  $repaymentPlan
     * @return void
     */
    public function created(RepaymentPlan $repaymentPlan)
    {
        //
    }

    /**
     * Handle the repayment plan "updated" event.
     *
     * @param  \App\Models\RepaymentPlan  $plan
     * @param  \App\Helpers\FinanceHandler $financeHandler
     * @return void
     */
    public function updated(RepaymentPlan $plan)
    {
        //
        $financeHandler =  new FinanceHandler(new TransactionLogger);
       
        if ($plan->wasChanged('paid_out')) {
           
            if ($plan->paid_out == true) {
                
                $user = $plan->loan->user;

                $this->settleAffiliateOnPlan($user, $plan, $financeHandler);
            }
            
        }
    }

    /**
     * Handle the repayment plan "deleted" event.
     *
     * @param  \App\Models\RepaymentPlan  $repaymentPlan
     * @return void
     */
    public function deleted(RepaymentPlan $repaymentPlan)
    {
        //
    }

    /**
     * Handle the repayment plan "restored" event.
     *
     * @param  \App\Models\RepaymentPlan  $repaymentPlan
     * @return void
     */
    public function restored(RepaymentPlan $repaymentPlan)
    {
        //
    }

    /**
     * Handle the repayment plan "force deleted" event.
     *
     * @param  \App\Models\RepaymentPlan  $repaymentPlan
     * @return void
     */
    public function forceDeleted(RepaymentPlan $repaymentPlan)
    {
        //
    }
}
