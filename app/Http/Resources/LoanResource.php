<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
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
            'reference'=>$this->reference,
            'amount'=>$this->amount,
            'duration'=>$this->duration,
            'name'=> $this->user->name
        ];
    }
}
