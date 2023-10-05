<?php

namespace App\Listeners\Investor;

use App\Models\Settings;
use App\Helpers\FinanceHandler;
use App\Traits\SettleAffiliates;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvestorWalletFundListener
{

    use SettleAffiliates;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(FinanceHandler $financeHandler)
    {
        //
        $this->financeHandler = $financeHandler;
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
        $investor = $event->investor;

        $amount = $event->amount;

        $this->settleAffiliateOnFunding($investor, $amount, $this->financeHandler);
        
    }
}
