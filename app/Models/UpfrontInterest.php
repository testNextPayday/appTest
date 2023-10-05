<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpfrontInterest extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'request_id','loan_id','user_id', 'interest', 'mgt_fee', 'loan_fee','total_payment','status'
    ];
    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class, 'request_id');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
