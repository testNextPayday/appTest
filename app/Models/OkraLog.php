<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OkraLog extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'user_id','repayment_plan_id','loan_id', 'emi', 'amount_paid', 'status','date_paid'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function repaymentPlan()
    {
        return $this->belongsTo(RepaymentPlan::class);
    }
}
