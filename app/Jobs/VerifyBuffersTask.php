<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Traits\DoesBufferVerification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\TransactionVerificationService;

class VerifyBuffersTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DoesBufferVerification;

    /**
     * 30mins
     *  
     * @var int
     */
    public $timeout = 1800;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TransactionVerificationService $service)
    {
        //
        $this->service = $service;
        $this->startVerification();
    }
}
