<?php
namespace App\Traits;

use App\Models\PenaltyEntry;
use App\Models\PenaltySetting;


trait LoanPenaltyMethods
{
    public function hasPenaltySettings()
    {
        if ($this->penaltySetting) {
            return true;
        }

        $employment = optional($this->loanRequest)->employment;
        if ($employment) {
            $employer = $employment->employer;
            if ($employer->penaltySetting) {
                return true;
            }
        }

        return false;
    }

   


  

    public function getWalletTransactionsForPenalty()
    {
        return $this->loanWalletTransactions()->whereIsPenalty(true)->get()->sortBy('collection_date');
    }

    public function getWalletTransactionsForPenaltySum()
    {
        return $this->loanWalletTransactions()->whereIsPenalty(true)->sum('amount');
    }

    public function penaltySetting()
    {
        return $this->morphOne(PenaltySetting::class, 'entity');
    }

}