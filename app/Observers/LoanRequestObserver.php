<?php

namespace App\Observers;

use App\Models\LoanRequest;

use App\Traits\ReferenceNumberGenerator;

class LoanRequestObserver
{
    use ReferenceNumberGenerator;
    
    /**
     * Handle the loan request "creating" event.
     *
     * @param  \App\Models\LoanRequest  $loanRequest
     * @return void
     */
    public function creating(LoanRequest $loanRequest)
    {
        $loanRequest->reference = $this->generateReference(LoanRequest::class, 'UC-LR-');
        $loanRequest->emi = $loanRequest->emi();
        return $loanRequest;
    }
    
    
    /**
     * Handle the loan request "created" event.
     *
     * @param  \App\Models\LoanRequest  $loanRequest
     * @return void
     */
    // public function creating(LoanRequest $loanRequest)
    // {
    //     $loanRequest->reference = $this->generateReference(LoanRequest::class, 'UC-LR-');
    //     return $loanRequest;
    // }
}
