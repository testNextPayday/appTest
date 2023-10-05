<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionInsightFormatter extends JsonResource
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
            'amount'=> $this->status == 1 ? ( $this->paid_amont ?? $this->total_amount) : $this->splitPayments, // amount user paid
            'status'=> $this->status,
            'splitPayments'=> $this->splitPayments,
            'plan_id'=> $this->id,
            'expected'=> $this->total_amount, // expected amount user should have paid
        ];
    }

}
