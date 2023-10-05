<?php

namespace App\Listeners;

use App\Jobs\AutomaticLoanFund;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutomaticLoanFundListener
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
        //AutomaticLoanFund::dispatch($event->loanRequest)->onQueue('LoanFunder');
    }
}
