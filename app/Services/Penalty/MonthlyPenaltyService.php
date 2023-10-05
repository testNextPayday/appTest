<?php 
namespace App\Services\Penalty;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\RepaymentPlan;
use App\Models\PenaltySetting;
use App\Services\PenaltyNotificationService;
use App\Services\Penalty\BuildPenaltyService;

/***
 *  This is responsible for accrueing penalties on a monthly basis
 */
class MonthlyPenaltyService
{

    protected $notifyService;

    public function __construct()
    {
        $this->notifyService = new PenaltyNotificationService();
    }

     /*
    * Get all repayments that are due and return only unique loan IDs
    *
    * @return array
    */
   public function penelableLoanIDs()
   {
       $today = Carbon::today();

       $loanIds = RepaymentPlan::whereDate('payday', '<=', $today)->whereStatus(false)->with('loan')->get()->filter(function($plan){ return optional($plan->loan)->status == '1'; })->pluck('loan.id')->unique();

       return $loanIds->toArray();
   }
        
    /**
     * Prepare loan id for prenalizing
     *
     * @return void
     */
    public function penalizeDefaultingLoans()
    {
        $penalableLoanIds = $this->penelableLoanIDs();
        
        $penalableLoans = [];

        foreach ($penalableLoanIds as $loanId) {

            $loan = Loan::with('repaymentPlans')->find($loanId);
            // Penalize loan
            $response = $loan->is_penalized ? $this->accrueLoanPenalty($loan) : $this->buildLoanPenalty($loan);
            
        }
    }

    
    /**
     * Build loan penalty
     *
     * @param  mixed $loan
     * @return void
     */
    public function buildLoanPenalty($loan)
    {
        (new BuildPenaltyService())->build($loan);
    }

    
    /**
     * Accrue Loan Penalty
     *
     * @param  mixed $loan
     * @return void
     */
    public function accrueLoanPenalty($loan)
    {
        (new AccruePenaltyService())->accrue($loan);
    }

    
    /**
     * penalize a loan
     *
     * @param  mixed $loan
     * @return void
     */
    public function penalizeLoan($loan)
    {
        // Retreive Settings and apply
        $setting  = (new PenaltySettingRetrieve($loan))->getSettings();
        
        // If no settings or settings not active
        if ( $setting == false || ($setting->status == 0 || $loan->status != '1')) {
            
            return false;
        }
       
        $this->initiatePenalty($setting, $loan);
        return false;
        
    }

    
    /**
     * Penalise new loans
     *
     * @param  mixed $loan
     * @param  mixed $setting
     * @return void
     */
    protected function initiatePenalty($setting, $loan)
    {

        //Get plans that has passed grace periods
        $plans = $this->getPassedGracePeriod($loan, $setting->grace_period);


        // Check for minimum emi count
        $minimumPaymentCount = $setting->unpaid_count;
        if ($plans->count() < $minimumPaymentCount) {
           
            return false;
        }

        $penalty = $this->getCalculatedPenaltyAmount($plans, $setting);
        // Order of this is important so we can get all penalties after this date_penalized when dissolving penalties.. milliseconds do matter
        $loan->is_penalized ?: $loan->update(['is_penalized'=> true, 'date_penalized'=>now()]);

        $this->applyPenaltyToLoan($loan, $penalty);

        $this->notifyService->notifyLoanPenalized($loan);
        
        return true;
    }
    
    /**
     * Get penalty amount to be debited the user
     *
     * @param  mixed $plans
     * @param  mixed $setting
     * @return void
     */
    protected function getCalculatedPenaltyAmount($plans, $setting) 
    {
        $totalOwed =  $plans->sum('total');
        // Create a new Penalty Entry
        $penaltyService = new PenaltyService();

        $penalAmount = $penaltyService->getPenalAmount($totalOwed, $setting);

        return $penalAmount;
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

    
   
    
    /**
     * Apply Penalty To Loan
     *
     * @param  mixed $loan
     * @param  mixed $amount
     * @return void
     */
    protected function applyPenaltyToLoan($loan, $amount)
    {
        $penaltyService = new PenaltyService();
        $desc = 'Total Penalty Due as at '.now()->format('D m, Y');
        $penaltyService->debitPenaltyCollection($loan, $amount, $desc);
        
    }
   

    
   
}