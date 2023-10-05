<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Facades\BulkSms;

class SendTermiiSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $readyNumbers;

    protected $message;

    protected $senderId;

    /**
     * 5mins
     *  
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($readyNumbers, $message, $senderId)
    {
        $this->readyNumbers  = $readyNumbers;
        $this->message = $message;
        $this->senderId = $senderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->readyNumbers as $number) {
            BulkSms::send($number, $this->message, $this->senderId);
        } 
    }
}
