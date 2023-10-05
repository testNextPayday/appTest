<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvestorStatisticResource extends JsonResource
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
            
            'wallet'=>$this->wallet,
            'vault'=>$this->vault,
            'incomeEarned'=> $this->incomeEarned(),
            'commissionsPaid'=> $this->commissionsPaid(),
            'commission_rate'=> $this->commission_rate,
            'portfolioSize'=> $this->portfolioSize(),
            'outstandingPrincipal'=> $this->outstandingPrincipal(),
            'outstandingInterest'=> $this->outstandingInterest(),
            'recoveries'=> $this->recoveriesMade(),
            'withdrawals'=> $this->getSuccessfulWithdrawals()->sum('amount'),
            'taxPaid'=> $this->taxPaid(),
            'tax_rate'=> $this->tax_rate,
            'auctionedLoans'=> $this->auctionedLoans(),
    
        ];
    }
}
