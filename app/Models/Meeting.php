<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
    
    protected $dates = ['when'];
    
    public function invitees()
    {
        return $this->hasMany(Affiliate::class);
    }
}
