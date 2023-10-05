<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ReferenceNumberGenerator;

class Settlement extends Model
{
    //
    protected $refPrefix = 'NPD-STM-';
    protected $guarded = [];
    use SoftDeletes,ReferenceNumberGenerator;


    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            $model->reference = $model->generateReference();
        });
    }

    public function getRouteKeyName()
    {
        return 'reference';
    }

    public function scopePending($query)
    {
        return $query->where('status',1);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status',2);
    }

    public function loan()
    {
        return $this->belongsTo('App\Models\Loan');
    }
}
