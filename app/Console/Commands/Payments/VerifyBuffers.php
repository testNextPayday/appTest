<?php

namespace App\Console\Commands\Payments;

use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;
use Illuminate\Console\Command;
use App\Traits\DoesBufferVerification;
use App\Services\TransactionVerificationService;

class VerifyBuffers extends Command
{
    use DoesBufferVerification;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:buffers';

    /**
     * The service will be be user to verify the buffers
     * 
     * @var \App\Services\TransactionVerificationService
     */
    protected $service;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies each buffer payment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TransactionVerificationService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       $this->startVerification();
    }
}
