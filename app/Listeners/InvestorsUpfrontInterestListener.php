<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Unicredit\Collection\InvestorsUpfrontInterest;

class InvestorsUpfrontInterestListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(InvestorsUpfrontInterest $investorsUpfrontInterest)
    {
        $this->upfrontInterest = $investorsUpfrontInterest;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $loan = $event->loan;        
        return $this->upfrontInterest->settleInvestors($loan);        
    }
}
