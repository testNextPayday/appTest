<?php

namespace App\Console\Commands\Remita;

use App\Helpers\Constants;

use App\Models\RepaymentPlan;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Remita\DDM\DebitOrderIssuer;
use App\Services\LoanRepaymentService;

class UpdateInstructions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remita:updateInstructions  {--borrower=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates instructions with special attention to the successful/rejected ones';

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

        $borrower = $this->option('borrower');
        $plans = $borrower ? $this->getUserRepaymentPlans($borrower) : $this->getAllDueRepaymentPlans();
       
        foreach($plans as $plan) {

            try {
                 // Skip Repayment if it does not belong to a Remita DDM Loan
                if (!optional($plan->loan)->hasApprovedCollectionMethod(Constants::DDM_REMITA)) continue;

                // check status
                $response = $issuer->statusInBits($plan);
                
                $statuscode = $response->getStatusCode();
                
                $update = [
                    'status_message' => $response->getMessage() . "($statuscode)",
                ];

                // loop through all buffers to know if all are statused 1
                $failed = $plan->buffers->where('status',0)->first();

                if(! $failed){
                    $repaymentService = new LoanRepaymentService();
                    session(['payment_method'=>'REMITA']);
                    $repaymentService->makePaymentFromWallet($plan);
                }

            }catch(\Exception $e){

                // if an exception occurs we will love to log it and continue
                Log::channel('remita')->debug($e);
                continue;
            }

           
    
        }
        
    }


    public function getUserRepaymentPlans($borrower)
    {
      
        $plans = DB::table('users')->where('users.id','=',$borrower)
                    ->join('loans','loans.user_id','=','users.id')
                    ->join('repayment_plans','repayment_plans.loan_id','=','loans.id')
                    ->select('repayment_plans.id')
                    ->whereNotNull('repayment_plans.transaction_ref')
                    ->whereNotNull('repayment_plans.requestId')
                    ->where('repayment_plans.status','=',0)
                    ->get()->pluck('id');
            $plans = $plans->map(function($item,$index){
                return RepaymentPlan::find($item);
            });

            return $plans;
    }


    public function getAllDueRepaymentPlans()
    {
        // 1. Get instructions with a txref and a requestId with status of 0
        $plans = RepaymentPlan::whereNotNull('transaction_ref')
                    ->whereNotNull('requestId')
                    ->whereStatus(0)
                    ->whereDate('created_at', '>', config('remita.new_implementation'))
                    ->get();
        return $plans;
    }
}
