<?php

namespace App\Http\Resources;

use App\Models\Loan;
use App\Helpers\Constants;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
       

        return [
            'id'=>$this->id,
            'owner_id'=>$this->owner_id,
            'owner_type'=>$this->owner_type,
            'amount'=> $this->amount,
            'reference'=>$this->reference,
            'transaction_id'=>$this->transaction_id,
            'description'=>$this->description,
            'pay_status'=>$this->pay_status,
            'created_at'=>$this->created_at->toDateString(),
            'updated_at'=>$this->updated_at->toDateString(),
            'status_message'=>$this->status_message,
            'link_id'=>$this->link_id,
            'link_type'=>$this->link_type,
            'link'=>$this->link,
            'collection_method'=> $this->link_type == 'App\Models\Loan' ? $this->getLoanCollectionMethod($this->link_id) : " ",
        ];
    }


    public function getLoanCollectionMethod($loanId) 
    {
       
        $loan = Loan::find($loanId);

        if (! $loan) {
            return " ";
        }

        return @Constants::generateCollectionCodeMap()[$loan->collection_plan];



    }
}
