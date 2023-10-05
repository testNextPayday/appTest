<?php

namespace App\Console\Commands\Sweepers;

use Illuminate\Console\Command;

use App\Unicredit\Collection\LoanSweeper;
use App\Unicredit\Collection\Utilities;
/**
 * WARNING : This class is deprecated and has not been updated
 * Please to resuse again do enure you go through needed measures
 */
class BucketPeakSweeper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sweep_peak:buckets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all due collections (by bucket peak periods) and hands them over for sweeping';

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
        $buckets = Utilities::getBucketsForPeakSweep();
        
        $loans = collect([]);
        
        foreach ($buckets as $bucket) {
            $employers = $bucket->employers;
        
            foreach($employers as $employer) {    
                $employeeLoans = $employer->getEmployeeUnfulfilledLoans();
                $loans = $loans->merge($employeeLoans);
            }    
        }
        
        
        $sweeper = new LoanSweeper();
        $sweeper->sweep($loans);
    }
}
