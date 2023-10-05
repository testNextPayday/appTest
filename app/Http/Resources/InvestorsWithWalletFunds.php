<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvestorsWithWalletFunds extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $code = config('unicredit.flow')['wallet_fund'];

        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'funds'=> $this->transactions->where('code', $code)
        ];
    }
}
