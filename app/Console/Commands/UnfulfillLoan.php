<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UnfulfillLoan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unfulfill:loan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pushes unconfirmed loans out of fulfilment';

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
        $fulfilledLoans = Loan::whereStatus("2")->with('repaymentPlans')->get();


        try {

            // extract ready fulfill loans
            $pushActiveLoans  = $fulfilledLoans->filter(
                function ($loan) {
                    $unpaidPlans = $loan->repaymentPlans->where('status', 0);
                    return $unpaidPlans->count() > 0;
                }
            );

            if ($pushActiveLoans->count() > 0) {

                foreach ($pushActiveLoans as $loan) {
                    
                    // marking loan as active
                    $loan->update(['status'=> '1']);
                }
            }
            

        } catch (\Exception $e) {

            Log::channel('unexpectederrors')->debug($e->getMessage());
        }

        print('Pushed '.$pushActiveLoans->count(). '  unfulfilled loans active');
    }
}
