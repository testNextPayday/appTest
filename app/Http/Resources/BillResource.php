<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
           
            'id'=>$this->id,
            'name'=>$this->name,
            'amount'=>$this->amount,
            'occurs'=>$this->occurs,
            'frequency'=>$this->frequency,
            'status'=>$this->status,
            'account_number'=>optional($this->banks->last())->account_number,
            'bank_id'=>optional($this->banks->last())->id,
            'bank_code'=>optional($this->banks->last())->bank_code,
            'bank_name'=>optional($this->banks->last())->bank_name,
            'created_at'=>date('D M d, Y h:i a,',strtotime($this->created_at)),
            'recipient_code'=>optional($this->banks->last())->recipient_code,
            'category'=> !is_null($this->bill_category_id) ? $this->category->name : null,
            'bill_category_id'=> $this->bill_category_id
       ];
    }
}
