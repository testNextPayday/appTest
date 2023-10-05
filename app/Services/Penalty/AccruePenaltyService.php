<?php 
namespace App\Services\Penalty;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\RepaymentPlan;
use App\Models\PenaltySetting;
use Illuminate\Support\Facades\Log;
use App\Services\PenaltyNotificationService;

/***
 *  This is responsible for accrueing penalties on a monthly basis
 */
class AccruePenaltyService
{

    protected $notifyService;

    protected $penaltyService;

    public function __construct()
    {
        $this->notifyService = new PenaltyNotificationService();

        $this->penaltyService = new PenaltyService();
    }


    
    /**
     * accrue penalty on a loan
     *
     * @param  mixed $loan
     * @return void
     */
    public function accrue(Loan $loan)
    {

        // Retreive Settings and apply
        $setting  = (new PenaltySettingRetrieve($loan))->getSettings();
        
        // If no settings or settings not active
        if ( $setting == false || ($setting->status == 0 || $loan->status != '1')) {
            
            return false;
        }

        //Get plans that has passed grace periods and not penalized
        $plans = $this->getPassedGracePeriod($loan, $setting->grace_period)->where('is_penalized', false);
       
        $this->initiateAccrueProcess($plans, $setting);

        return true;
    }


    public function initiateAccrueProcess($plans, $setting)
    {
        $loan = $plans->first()->loan;

        $totalDebts = $loan->total_penable_debts;
      

        $penalty = 0;

        foreach($plans as $plan) {

            if ($plan->is_penalized) { continue;}

            $totalDebts += $plan->total_amount + $penalty;

            $penalty = $this->penaltyService->getPenalAmount($totalDebts, $setting);

            $desc = 'Total Penalty Due as at '.$plan->payday;

            $this->penaltyService->debitPenaltyCollection($plan->loan, $penalty, $desc);
            unset($plan->total);
            //Mark as penalized
            $plan->update(['is_penalized'=> true]);
        }

        $loan->update(['total_penable_debts'=> $totalDebts + $penalty]);
    }
        

    
    /**
     * Retrieves plans that has passed grace period
     *
     * @param  mixed $loan
     * @param  mixed $gracePeriod
     * @return void
     */
    protected function getPassedGracePeriod($loan, $gracePeriod)
    {
        return $loan->repaymentPlans->where('status', 0)->filter(function($plan) use ($gracePeriod){
            $plan->total = $plan->total_amount;
            $today = Carbon::today();
            return $plan->payday->diffInDays($today) >= $gracePeriod && $plan->is_penalized == false && $plan->payday < $today;
        });
    }
   

    
   
}