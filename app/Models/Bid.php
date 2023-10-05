<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $guarded = [];
    
    protected $dates = [
        'due_date'  
    ];
    
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
    
    public function loanFund()
    {
        return $this->belongsTo(LoanFund::class, 'fund_id');
    }
}
