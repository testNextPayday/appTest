<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\RepaymentPlan;
use App\Jobs\VerifyBuffersTask;
use Illuminate\Console\Command;
use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Support\Facades\Artisan;
use App\Unicredit\Collection\CardService;

class SweepAutoSweepingLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sweep:auto-loans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sweeps Loans with auto sweeping';

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
        $cardService = new CardService(new Client, new DatabaseLogger);
        
        $today = Carbon::today();

        // 1. Get all due repaymentPlans
        // Include orders that have insufficient funds (Temporarily)
        RepaymentPlan::with('loan')->whereHas('loan', function($query){
            return $query->where(['auto_sweeping'=> 1, 'is_penalized'=>0]);
        })
            ->where('status', false)
            ->where('payday', '<=', $today)
            ->where('should_cancel', 0)
            ->chunk(50, function($plans) use($cardService){
                // 2. Loop through repayment plans
                foreach ($plans as $plan) {
                    
                    // 3. Check if plan can be charged by card and attempt a charge
                    if ($plan->canMakeCardAttempt()) {
                        $attempt = $cardService->attemptInBits($plan);
                    }
                }
            });
        
        // Dispatch a verify buffer job
        dispatch(new VerifyBuffersTask())
        ->delay(60);
    }


     
}
