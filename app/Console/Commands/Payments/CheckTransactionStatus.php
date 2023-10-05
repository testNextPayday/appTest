<?php

namespace App\Console\Commands\Payments;

use Illuminate\Console\Command;
use App\Models\GatewayTransaction;
use App\Traits\ChecksPaymentStatus;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Contracts\MoneySender;

class CheckTransactionStatus extends Command
{

    use ChecksPaymentStatus;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the status of pending transactions and verify them and updates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MoneySender $channel, DatabaseLogger $dbLogger)
    {
        parent::__construct();

        $this->channel = $channel;

        $this->dbLogger  = $dbLogger;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        try{

            $transactionRefs = GatewayTransaction::pending()
            ->get()->pluck('reference')->toArray();
           
            $this->checkPaymentStatus($transactionRefs);

        }catch(\Exception $e){

            //TODO : Find something serious to do with this code
        }
       
    }
}
