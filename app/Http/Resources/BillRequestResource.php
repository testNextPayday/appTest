<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillRequestResource extends JsonResource
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
            'id'=> $this->id,
            'bill_id'=> $this->bill->id,
            'name'=> $this->bill->name,
            'date'=> $this->created_at->format('d/m/Y a'),
            'status'=> $this->status,
            'amount'=> $this->bill->amount
        ];
    }
}
