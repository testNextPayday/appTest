<?php

namespace App\Console\Commands\Remita;

use App\Helpers\Constants;

use App\Models\RepaymentPlan;

use Illuminate\Console\Command;
use App\Remita\DDM\DebitOrderIssuer;
use Illuminate\Support\Facades\Log as sysLog;

class CancelInstructions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remita:cancelInstructions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancels instructions scheduled for cancellation';

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
        $issuer = new DebitOrderIssuer();
        
        
        // 1. Get all plans scheduled for cancellation
        $plans = RepaymentPlan::where('should_cancel', true)
                        ->whereNotNull('requestId')
                        ->whereDate('created_at', '>', config('remita.new_implementation'))
                        ->where('cancelled', false)->get();
                                   
        foreach($plans as $plan) {
            
            try{

                // Skip Repayment if it does not belong to a Remita DDM Loan
                if (!optional($plan->loan)->hasApprovedCollectionMethod(Constants::DDM_REMITA)) continue;

                $response = $issuer->cancelInBits($plan);
            
                // if all bits are cancelled then cancell
                if ($plan->buffers->where('cancelled',false)->first() == null) {
                    $plan->update(['cancelled' => true]);
                }

            }catch(\Exception $e){
                //any error report and continue when i come i know what to do
                sysLog::channel('remita')->info($e->getMessage());
                continue;
            }
            
           
        }
    }
}
