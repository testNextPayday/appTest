<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvestorBank extends JsonResource
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
            'name'=> $this->name,
            'id'=> $this->id,
            'reference'=> $this->reference,
            'bank'=> $this->banks->last() ? $this->banks->last()->toArray() : []
        ];
    }
}
