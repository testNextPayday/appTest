<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateCommissionResource extends JsonResource
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
            'entity'=>$this->entity,
            'id'=>$this->id,
            'amount'=>$this->amount,
            'loan'=>$this->entity,
            'user'=>@$this->entity->user ? $this->entity->user : null,
            'created_at'=> date('Y m d', strtotime($this->created_at)),
            'description'=>$this->description,
            'reference'=>$this->reference
        ];
    }
}
