<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanPenaltyDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'reference'=> $this->reference,
            'entries'=> $this->getWalletTransactionsForPenalty(),
            'amountOwed'=> $this->user->loan_wallet,
            'sumDue'=> $this->getWalletTransactionsForPenaltySum(),
        ];
    }
}
