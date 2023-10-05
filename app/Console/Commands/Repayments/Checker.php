<?php

namespace App\Console\Commands\Repayments;

use Illuminate\Console\Command;

use App\Unicredit\Collection\RepaymentManager;
use App\Models\Loan;

class Checker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repayments:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks DDM Repayments';

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
        $loans = Loan::unfulfilled()->ddm()->get();
        $manager = new RepaymentManager();
        $manager->checkRepayments($loans);
    }
}
