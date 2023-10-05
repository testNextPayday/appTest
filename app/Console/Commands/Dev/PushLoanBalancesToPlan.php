<?php

namespace App\Console\Commands\Dev;

use Carbon\Carbon;
use App\Models\RepaymentPlan;
use Illuminate\Console\Command;
use App\Services\LoanRepaymentService;

class PushLoanBalancesToPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:loan-wallets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl Loan Wallets and confirms balance on plan';

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
    public function handle(LoanRepaymentService $loanService)
    {
        //
        $today = Carbon::today();
        RepaymentPlan::whereStatus(false)->whereDate('payday', '<=', $today)
        ->with('loan.user')->chunk(50, function($plans) use($loanService){
            
            foreach($plans as $plan) {

                try {

                    $loan = $plan->loan;
                    $user  = isset($loan) ? optional($loan->user)->fresh() : null;
                    if (isset($loan) && $loan->isActive() && ($user->loan_wallet > 0 )) {
                        
                        $loanService->makePaymentFromWallet($plan);
                    }
                    
                }catch(\Exception $e) { print($e->getMessage()); continue;}
                
            }
        });
    }
}
