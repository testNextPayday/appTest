<?php

namespace App\Console\Commands\Collectors;

use Carbon\Carbon;

use GuzzleHttp\Client;

use App\Models\RepaymentPlan;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Collection\CardService;

/**
 * WARNING : This class is deprecated and has not been updated
 * Please to resuse again do enure you go through needed measures
 */
class Card extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:card';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attempts card collections on due loan Repayment Plans';

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
        $plans = $this->getPlans();

        
        // 2. Loop through repayment plans
        foreach ($plans as $plan) {
            
            // 3. Check if plan can be charged by card and attempt a charge
            if ($plan->canMakeCardAttempt()) {
                
                //$attempt = $cardService->attempt($plan);
                $attempt = $cardService->attemptInBits($plan);
        
            }
        }
    }

    
    /**
     * Get plans that are to be swept
     *
     * @return void
     */
    protected function getPlans()
    {
        $today = Carbon::today();
        
        $plans = RepaymentPlan::with('loan')
            ->where('status', false)
            ->where('payday', '<=', $today)
            ->where('should_cancel', 0)
            ->get();

        return $plans->filter(function($plan) {
            return optional($plan->loan)->collection_plan == 300 && optional($plan->loan)->pause_sweep == false ;
        });
    }
}
