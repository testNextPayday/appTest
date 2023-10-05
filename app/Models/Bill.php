<?php

namespace App\Models;

use App\Traits\Payable;
use App\Models\BankDetail;
use App\Models\BillCategory;
use App\Traits\HasGatewayRecords;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    //

    use Payable, HasGatewayRecords;
    
    protected $guarded = [];

    
    /**
     * Monthly automated bills
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeMonthly($query)
    {
        return $query->where('status', 1)
            ->where('occurs', 'monthly')
            ->where('frequency', 'always');
    }
    
    /**
     * Weekly automated bills
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeWeekly($query)
    {
        return $query->where('status', 1)
            ->where('occurs', 'weekly')
            ->where('frequency', 'always');
    }

    
    /**
     * Pending Bills
     *
     * @param  mixed $query
     * @return void
     */
    public function requests()
    {
        return $this->hasMany(BillApprovalRequest::class);
    }



    public function getRouteKeyName()
    {
        return 'id';
    }


    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }


    public function banks()
    {
        return $this->morphMany(BankDetail::class, "owner");
    }

    
    /**
     * Checks if a bill is missing recipient code
     * on bank details
     *
     * @return void
     */
    public function isMissingRepCode()
    {
        return ! isset($this->banks->last()->recipient_code);
    }
    
    /**
     * Category of bill
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(BillCategory::class, 'bill_category_id');
    }

   
}
