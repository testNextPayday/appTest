<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoanTransaction extends Model
{
    protected $guarded = [];
    //


    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
