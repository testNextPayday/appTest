<?php

namespace App\Listeners;

use App\Facades\OTPSms;
use App\Facades\BulkSms;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleOTPListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $this->payload = $event->data;

        $number = make_smsable(@$event->data['phone']);

        $message = $event->data['message'];

        OTPSms::send($number, $message);
        
    }


    
}
