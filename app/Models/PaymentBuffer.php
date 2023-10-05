<?php

namespace App\Models;


use App\Traits\Visitable;
use App\Models\RepaymentPlan;
use Illuminate\Database\Eloquent\Model;

class PaymentBuffer extends Model
{
    //

    use Visitable;

    protected $guarded = [];
    
    /**
     * Get only unverified payment buffers
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeUnverified($query)
    {
        return $query->where('verified', 0);
    }

    
    /**
     * The ones we want to verify
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeVerifiable($query)
    {
        return $query->where('verified', 0)->orWhere('status', 0);
    }

    public function plan()
    {
        return $this->belongsTo(RepaymentPlan::class, 'plan_id');
    }
}
