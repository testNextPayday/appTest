<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Target extends Model
{
    //

    use SoftDeletes;

    protected $guarded = [];

    
    /**
     *  Gets all user attached to this target
     *
     * @return void
     */
    public function users()
    {
        return $this->morphedByMany(Affiliate::class, 'targettable');
    }


    
    /**
     * Returns all active targets
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    
    /**
     * Get all targets that expired the day before
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeExpiredYesterday($query)
    {
        $yesterday = Carbon::now()->subDays(1)->toDateString();
        return $query->where('expires_at', $yesterday);
    }
}
