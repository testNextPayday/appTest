<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Services\RefundTransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaystackCustomerRefundListener
{

    public $refundService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(RefundTransactionService $refundService)
    {
        //
        $this->refundService = $refundService;
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
        $trnxRef = $event->reference;

        $user = $event->user;

        $this->refundService->refund($trnxRef, $user);
    }
}
