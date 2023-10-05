<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\ReferenceNumberGenerator;

class InvestorVerificationRequest extends Model
{
    use ReferenceNumberGenerator;
    
    protected $guarded = [];
    
    protected $refPrefix = 'UIV-';
    
    public function getRouteKeyName()
    {
        return 'reference';
    }
    
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            $model->reference = $model->generateReference();
        });
    }
    
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
