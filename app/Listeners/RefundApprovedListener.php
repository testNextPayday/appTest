<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Unicredit\Payments\NextPayClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefundApprovedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(NextPayClient $paymentClient)
    {
        //
        $this->paymentClient = $paymentClient;
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
        try{

            $refund = $event->refund;

            $response = $this->paymentClient->payRefund($refund);
    
            session()->flash('info', $response['message']);

        }catch(\Exception $e){

            if($e instanceof ClientException){

                $err = json_decode((string)$e->getResponse()->getBody());
                $msg = $err->message;
                
            }else{
    
                $msg = $e->getMessage();
            }
            session()->flash('failure', $msg);

            // update refund status

            $refund->update(['status'=>0]);
        }
       
    }
}
