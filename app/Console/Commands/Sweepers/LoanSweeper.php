<?php

namespace App\Console\Commands\Sweepers;

use Illuminate\Console\Command;

use App\Unicredit\Collection\Utilities;
use App\Unicredit\Collection\LoanSweeper as Sweeper;

/**
 * WARNING : This class is deprecated and has not been updated
 * Please to resuse again do enure you go through needed measures
 */

class LoanSweeper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sweep:loans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all due collections and hands them over for sweeping';

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
        $loans = Utilities::getLoansForSweep();
        $sweeper = new Sweeper();
        $sweeper->sweep($loans);
    }
}
