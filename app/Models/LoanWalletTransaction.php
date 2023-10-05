<?php

namespace App\Models;

use App\Models\Loan;
use App\Models\User;
use App\Models\RepaymentPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanWalletTransaction extends Model
{
    //
    use SoftDeletes;

    protected $guarded = [];

    
    /**
     * Get logged transactions awaiting approval and confirmation hide settlements
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeLogged($query)
    {
        return $query->where('is_logged', 1)->whereNotIn('collection_method', ['Settlement']);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function plan()
    {
        return $this->belongsTo(RepaymentPlan::class);
    }

    public function type() 
    {
        return $this->direction == 1 ? 'credit' : 'debit';
    }
}
