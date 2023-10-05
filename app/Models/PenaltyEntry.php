<?php

namespace App\Models;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Model;

class PenaltyEntry extends Model
{
    //
    protected $guarded = [];


    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
