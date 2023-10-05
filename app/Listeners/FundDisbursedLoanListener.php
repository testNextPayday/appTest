<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Unicredit\Payments\NextPayClient;
use Illuminate\Contracts\Queue\ShouldQueue;

/** This listener will put send money to user account */

class FundDisbursedLoanListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(NextPayClient $paymentClient){    //
        $this->paymentClient = $paymentClient;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event){
        //        
        $loan = $event->loan;        
        $response  = $this->paymentClient->pushMoney($loan);
        session()->flash('info', $response['message']);        
    }
}
