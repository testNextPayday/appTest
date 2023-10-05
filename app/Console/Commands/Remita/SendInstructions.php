<?php

namespace App\Console\Commands\Remita;

use Carbon\Carbon;

use App\Models\User;
use App\Helpers\Constants;

use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;

use App\Remita\RemitaResponse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Remita\DDM\DebitOrderIssuer;
use App\Unicredit\Logs\DatabaseLogger;
use Illuminate\Support\Facades\Log as sysLog;

class SendInstructions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remita:sendInstructions {--borrower=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends debit instructions to remita if user for  a particular user';

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
        $dbLogger = new DatabaseLogger();
        $today = Carbon::today();

        $borrower = $this->option('borrower');
        $plans = isset($borrower) ? $this->getUserRepaymentPlans($borrower) : $this->getAllDueRepaymentPlans(); 
        
        $problemLoans = config('unicredit.problem_loans');     
       
           
            foreach($plans as $plan) {
                
                try {
                    
                     // send issue
                    // Temp: Skip problem loans
                    if (in_array($plan->loan_id, $problemLoans)) continue;
                    
                    // Skip Repayment if it does not belong to a Remita DDM Loan
                    if (!optional($plan->loan)->hasApprovedCollectionMethod(Constants::DDM_REMITA)) continue;
                   
                    $responses = $issuer->issueInBits($plan);
                   
                    $dbLogger->log($plan, $responses, 'debit-order');

                                        
                    if($responses->isASuccess()) {
                    
                        $plan->update([
                            'order_issued' => true, 
                            'rrr' => $responses->getRRR(), // we will be getting the first
                            'transaction_ref' => $responses->getTransactionRef(),
                            'requestId' => $responses->getRequestId(),
                            'status_message' => $responses->getMessage()
                        ]);
                        
                    } else {
                        //if none succeded then we should clean the buffers
                        $plan->clearBuffers();
                        
                        $plan->update(['status_message' => $responses->getMessage()]);
                        
                    }
                }catch(\Exception $e){
                    
                    //any error occurs report and continue
                    sysLog::channel('remita')->debug($e);
                    continue;
                }
               
                
            }
       
       
    }


    protected function getUserRepaymentPlans($borrower)
    {
        $today = Carbon::now();
        $plans = DB::table('users')->where('users.id','=',$borrower)
                    ->join('loans','loans.user_id','=','users.id')
                    ->join('repayment_plans','repayment_plans.loan_id','=','loans.id')
                    ->select('repayment_plans.id')
                    ->where('repayment_plans.status',false)
                    ->where('order_issued',false)
                    ->whereDate('payday','<=',$today)
                    ->get()->pluck('id');
            $plans = $plans->map(function($item,$index){
                return RepaymentPlan::find($item);
            });

            return $plans;
    }

    


    protected function getAllDueRepaymentPlans()
    {
        $today = Carbon::now();
        $plans = RepaymentPlan::where('order_issued', false)
                ->where('status', false)
                ->whereDate('payday', '<=', $today)
                ->whereDate('created_at', '>', config('remita.new_implementation')) // remita implementation date
                ->with('loan')
                ->get()->filter(function($plan) { return optional($plan->loan)->remita_active == true;});
        return $plans;
    }
}