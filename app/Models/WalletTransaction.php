<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Traits\ReferenceNumberGenerator;

class WalletTransaction extends Model
{
    // use ReferenceNumberGenerator;
    protected $cast = [
        'amount'=>'integer'
    ];
    // protected $refPrefix = 'UC-WT-';
    
    // public static function boot()
    // {
    //     parent::boot();
    //     self::creating(function($model) {
    //         $model->reference = $model->generateReference();
    //     });
    // }
    
    protected $guarded = [];
    
    public function owner()
    {
        return $this->morphTo();
    }


    public function getRouteKeyName()
    {
        return 'reference';
    }

    public function entity()
    {
        return $this->morphTo();
    }

    public function scopeCommissions($query)
    {
        return $query->where('code', '019')->orWhere('code', '020')->orWhere('code', '032')->orWhere('code', '034');
    }

    public function scopePortfolioFees($query)
    {
        return $query->where('code', '027');
    }
}