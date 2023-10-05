<?php

namespace App\Console\Commands\Bills;

use App\Services\BillService;
use Illuminate\Console\Command;
use App\Unicredit\Payments\NextPayClient;

class PayWeeklyBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weekly:bills';

    /**
     * Injected service to enable payment
     *
     * @var \App\Services\BillService
     */
    protected $service;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay all automated weekly bills';

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
    public function handle(NextPayClient $paymentClient)
    {
        //
        $this->service = new BillService($paymentClient);
        $this->service->payWeeklyBills();
    }
}
