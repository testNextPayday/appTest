<?php

namespace App\Console\Commands\Sweepers;

use Illuminate\Console\Command;

use App\Models\Employer;

use App\Unicredit\Collection\LoanSweeper;
use App\Unicredit\Collection\Utilities;

/**
 * WARNING : This class is deprecated and has not been updated
 * Please to resuse again do enure you go through needed measures
 */

class EmployerSweeper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sweep:employers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all due collections (by employers) and hands them over for sweeping';

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
        $employers = Utilities::getEmployersForSweep();
        
        $loans = collect([]);
        
        foreach($employers as $employer) {    
            $employeeLoans = $employer->getEmployeeUnfulfilledLoans();
            $loans = $loans->merge($employeeLoans);
        }
        
        $sweeper = new LoanSweeper();
        $sweeper->sweep($loans);
        
    }
}
