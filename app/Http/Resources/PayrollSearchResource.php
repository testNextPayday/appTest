<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollSearchResource extends JsonResource
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
            'payroll_id'=>$this->payroll_id,
            'name'=>$this->user->name,
            'loan'=>optional($this->user->activeLoans())->last(),
            'reference'=>$this->user->reference
        ];
    }
}
