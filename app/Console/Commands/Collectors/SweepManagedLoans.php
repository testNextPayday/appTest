<?php

namespace App\Console\Commands\Collectors;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\RepaymentPlan;
use App\Jobs\VerifyBuffersTask;
use Illuminate\Console\Command;
use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Support\Facades\Artisan;
use App\Unicredit\Collection\CardService;

class SweepManagedLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sweep:managed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sweeps due managed loans';

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
        $cardService = new CardService(new Client, new DatabaseLogger);
        
        $today = Carbon::today();

        // 1. Get all due repaymentPlans
        // Include orders that have insufficient funds (Temporarily)
        RepaymentPlan::with('loan')->whereHas('loan', function($query){
            return $query->where('is_managed', 1);
        })
        ->where('status', false)
        ->where('payday', '<=', $today)
        ->where('should_cancel', 0)
        ->chunk(50, function($plans) use($cardService){
             // 2. Loop through repayment plans
            foreach ($plans as $plan) {
    
                // 3. Check if plan can be charged by card and attempt a charge
                if ($plan->canMakeCardAttempt()) {
                    
                    //$attempt = $cardService->attempt($plan);
                    if (optional($plan->loan)->is_managed) {
                        
                        $attempt = $cardService->attemptInBits($plan);
                    }
                    
                }
            }
        });

        // Dispatch a verify buffer job
        dispatch(new VerifyBuffersTask())
        ->delay(60);
    }

}
