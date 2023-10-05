<?php

namespace App\Console\Commands\Repayments;

use DB, Log;

use App\Models\RepaymentPlan;


use Illuminate\Console\Command;
use App\Unicredit\Managers\PayInvestorManager;

class SettleInvestors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var $managerstring
     */
    protected $signature = 'investors:settle  {--mode=backend}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks unsettled collections and settles investors';

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
    public function handle(PayInvestorManager $manager)
    {
        $mode = $this->option('mode');
       
        $manager->issueRepaymentProcess($mode);
    }
}
