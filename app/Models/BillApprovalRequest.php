<?php

namespace App\Models;

use App\Models\Bill;
use Illuminate\Database\Eloquent\Model;

class BillApprovalRequest extends Model
{
    //
    protected $guarded = [];
    

    public function getRouteKeyName()
    {
        return 'id';
    }
    
    /**
     * Pending Bill Requests
     *
     * @param  mixed $query
     * @return void
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

     /**
     * Declined Bill Requests
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeDeclined($query)
    {
        return $query->where('status', 'declined');
    }
    
    /**
     * Get the owning bill
     *
     * @return void
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
