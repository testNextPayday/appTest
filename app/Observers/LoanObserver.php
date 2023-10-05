<?php

namespace App\Observers;

use App\Models\Loan;
use App\Helpers\Constants;
use App\Remita\DDM\MandateManager;
use Illuminate\Support\Facades\Log;
use App\Unicredit\Logs\DatabaseLogger;
use App\Events\UpdateLoanCollectionMethodEvent;

class LoanObserver
{
    /**
     * Handle the loan "created" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function created(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "updated" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function updated(Loan $loan)
    {
      
       
    }

    /**
     * Handle the loan "deleted" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function deleted(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "restored" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function restored(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "force deleted" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function forceDeleted(Loan $loan)
    {
        //
    }
}
