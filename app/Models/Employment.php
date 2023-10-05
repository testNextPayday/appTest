<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Employment extends Model
{
    protected $guarded = [];
    
    protected $with = ['employer'];
    
    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getEmploymentLetterAttribute($value)
    {
        return asset(Storage::url($value));
    }
    
    public function getConfirmationLetterAttribute($value)
    {
        return asset(Storage::url($value));
    }
    
    public function getWorkIdCardAttribute($value)
    {
        return asset(Storage::url($value));
    }
}
