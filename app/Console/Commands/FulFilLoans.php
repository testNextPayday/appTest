<?php

namespace App\Console\Commands;

use App\Models\Loan;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Unicredit\Utils\FulfillingService;
use App\Services\MonoStatement\BaseMonoStatementService;

class FulFilLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:fulfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Circles through all loans and pushing them to fullfil';

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
    public function handle(FulfillingService $service)
    {
        //
        $activeLoans = Loan::whereStatus("1")->with('repaymentPlans')->get();


        try {

            Loan::whereStatus("1")->with('repaymentPlans')->chunk(100, function($activeLoans) use($service){

                foreach($activeLoans as $loan) {

                    $totalPlans = $loan->repaymentPlans;
                    $paidPlans = $loan->repaymentPlans->filter(function($repayment){
                        return $repayment->status == 1;
                    });

                    if ($totalPlans->count() == $paidPlans->count()) {

                         try {
                             $service->fulfill($loan);
                         }catch(\Exception $e) {
                             continue;
                         }
                    }

                    if($loan->collection_plan == 102){
                        $monoservice = new BaseMonoStatementService(new Client);
                        $reference = $loan->mono_payment_reference;
                        $monoservice->cancelMandate($reference);
                        
                    }
                }
            });   

        } catch (\Exception $e) {

            Log::channel('unexpectederrors')->debug($e->getMessage());
        }

       
      
    }
}
