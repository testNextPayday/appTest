<?php

namespace App\Console\Commands;

use App\Models\LoanFund;
use Illuminate\Console\Command;

class FulfillLoanFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fulfill:funds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for funds that should be in fulfill';

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
        LoanFund::active()->with('loanRequest.loan')->chunk(100, function($funds){
            
            foreach($funds as $fund) {
                // Push the fund fulfilled
                if (optional($fund->loanRequest->loan)->status == '2') {
                    continue;
                }
    
                $fund->update(['status'=> '6']);
                
            }
        });
        
    }
}
