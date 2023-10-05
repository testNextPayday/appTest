<?php

namespace App\Console\Commands\Repayments;

use Carbon\Carbon;
use App\Models\RepaymentPlan;
use Illuminate\Console\Command;
use App\Notifications\Users\FailedRepaymentsNotification;

class NotifyFailedRepayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:failed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies a user that their repayment has failed';

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
        $today = Carbon::now()->toDateString();

        $plans = RepaymentPlan::has('loan')->where('payday',$today)->where('status',false)->get();

        foreach($plans as $index=>$plan){

            $user = $plan->loan->user;

            $user->notify(new FailedRepaymentsNotification($plan));
        }
    }
}
