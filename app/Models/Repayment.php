<?php

namespace App\Models;

use App\Helpers\ReversePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repayment extends Model
{
    protected $guarded = [];
    
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);   
    }
    
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
    
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
    
    public function fund()
    {
        return $this->belongsTo(LoanFund::class,'fund_id');
        
    }
    
    public function plan()
    {
        return $this->belongsTo(RepaymentPlan::class, 'plan_id');
    }


    public function reverse()
    {

        return ReversePayment::reverse($this);
    }
    
}
