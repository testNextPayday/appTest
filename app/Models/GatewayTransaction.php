<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayTransaction extends Model
{
    protected $guarded = [];


    public function getRouteKeyName()
    {
        return 'id';
    }


    public function scopePending($query)
    {
        return $query->where('status_message', 'pending');
    }

    
    /**
     * Get us failed transactions
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeFailed($query)
    {
        return $query->where('status_message', 'failed');
    }

    public function scopeOtp($query)
    {
        return $query->where('status_message', 'otp');
    }

    
    /**
     * So here we get those transactions that did not fail and are not yet successful
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeHanging($query) 
    {
        return $query->where('pay_status', 0)->whereIn(
            'status_message', ['otp', 'pending']
        );
    }
    
    /**
     *  All transactions that are not successful
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeNotSuccessful($query)
    {
        return $query->where('pay_status', 0);
    }

    public function owner()
    {
        return $this->morphTo();
    }

    public function link()
    {
        return $this->morphTo();
    }
}
