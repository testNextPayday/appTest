<?php

namespace App\Console\Commands;

use App\Models\Loan;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Paystack\PaystackService;
use App\Unicredit\Logs\DatabaseLogger;
use App\Services\PenaltyCardSweepService;
use App\Unicredit\Collection\CardService;

class SweepPenableLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sweep:penalties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run sweeps on loans that are on penalties';

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
        
        $penaltyCardSweeper = new PenaltyCardSweepService(new PaystackService(new Client));
        
        $loans = Loan::penalized()->get();
        
        // 2. Loop through penalized loans
        foreach ($loans as $loan) {
            
            $penaltyCardSweeper->sweepLoan($loan);
        }
    }


}
