<?php
namespace App\Unicredit\Collection;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\RepaymentPlan;
use App\Unicredit\Contracts\CardSweeper;
use App\Unicredit\Collection\CardService;


class CardServiceDecorator 
{

    public function __construct(CardSweeper $sweeper)
    {
        $this->sweeperService = $sweeper;
    }


    public function sweepLoan(Loan $loan)
    {
        $today = Carbon::today();
        
        $loanSweepCounts = 0;

        $plans = $loan->repaymentPlans->where('status',false)->where('payday','<=',$today);

        foreach($plans as $plan){

            if($plan->canMakeCardAttempt() && $loan->is_penalized == 0){
                
                $attempt = $this->sweeperService->attemptInBits($plan);

                if ($attempt['status']) {

                    $loanSweepCounts++;

                    $this->updatePlan($plan);
                }

            }
           
        }

        return $loanSweepCounts;

    }


    protected function updatePlan(RepaymentPlan $plan)
    {
        $update = [
            'status_message' => 'Success',
            'date_paid' => Carbon::now()->toDateTimeString(),
            'status' => true,
            'collection_mode' => 'PAYSTACK',
            'paid_amount'=>$plan->emi,
            // schedule for cancellation only if a debit instruction has been issued
            'should_cancel' => $plan->order_issued ? true: false
        ];
        
        $plan->update($update);
    }
}

?>