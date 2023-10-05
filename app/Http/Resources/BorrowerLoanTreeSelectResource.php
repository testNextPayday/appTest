<?php

namespace App\Http\Resources;

use App\Http\Resources\LoanResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowerLoanTreeSelectResource extends JsonResource
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
            
            'name'=>$this->name,
            'reference'=>$this->reference,
            'loan'=>optional($this->activeLoans())->last()
        ];
    }
}
