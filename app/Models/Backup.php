<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Backup extends Model
{
    //

    use SoftDeletes;

    protected $guarded = [];

    
    /**
     * Daily Backups
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeDaily($query)
    {
        return $query->where('backup_frequency', 'daily');
    }


    /**
     * Weekly Backups
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeWeekly($query)
    {
        return $query->where('backup_frequency', 'weekly');
    }


    /**
     * Monthly Backups
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeMonthly($query)
    {
        return $query->where('backup_frequency', 'monthly');
    }

     /**
     * Monthly Backups
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeYearly($query)
    {
        return $query->where('backup_frequency', 'yearly');
    }


    
}
