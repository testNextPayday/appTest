<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\RepaymentPlan;
use Illuminate\Console\Command;
use App\Services\Penalty\PenaltyService;
use App\Services\Penalty\PenaltySettingRetrieve;

class CheckForPenalablePlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plans:penable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily checks penalised loan plans to penalised';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $today = Carbon::today();
        // Get due plans on loans that have been penalized
        $plans = RepaymentPlan::whereDate('payday', '<=', $today)->whereStatus(false)->with('loan')->get()->filter(function($plan){ return optional($plan->loan)->is_penalized; });

        
        // Create a penalty entry and update their penal balance
        foreach($plans as $plan)
        {
            $loan = $plan->loan;
            $setting = (new PenaltySettingRetrieve($loan))->getSettings();

            $gracePeriod = $setting->grace_period;
            $daysPassedExpiration = $plan->payday->diffInDays($today);
            
            // If plan is less than grace period or has been penalized continue
            if ($daysPassedExpiration < $gracePeriod || $plan->is_penalized == true) {
                continue;
            }

            // Create a new Penalty Entry for new due repayment
            $penaltyService = new PenaltyService();

            if($penaltyService->saveEntry($plan->total_amount, $loan, 'repayment')){
                
                $plan->update(['is_penalized'=> 1]);

                $loan->update(
                    ['date_penalized'=> now()]
                );
            }

        }
    }
}
