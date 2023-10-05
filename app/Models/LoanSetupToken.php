<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanSetupToken extends Model
{
    //

   

    protected $fillable = [
        'loan_id',
        'user_id',
        'collection_plan'
    ];


    public function loan()
    {
        return $this->belongsTo(\App\Models\Loan::class);
    }


    public function user()
    {
        return $this->belongsTo(\App\Models\Loan::class);
    }
}
