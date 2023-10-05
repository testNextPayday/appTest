<?php

namespace App\Listeners;

use App\Services\LoanRepaymentService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmPlanFromWalletListener
{
    protected $service;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(LoanRepaymentService $service)
    {
        //
        $this->service = $service;
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
        $trnx = $event->trnx;

        $data = $event->data;

        if ($data instanceof \App\Models\Settlement) {
             return $this->service->makeSettlementConfirmation($data);
        }

        // a topup scenerio
        if($data instanceof \App\Models\Loan) {
             return $this->service->makeTopupConfirmation($data);
        }

        return $this->service->makeConfirmation($trnx);
    }
}
