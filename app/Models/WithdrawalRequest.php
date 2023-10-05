<?php

namespace App\Models;

use App\Traits\HasGatewayRecords;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawalRequest extends Model
{
    
    use SoftDeletes, ReferenceNumberGenerator,HasGatewayRecords;
    
    protected $guarded = [];
    
    protected $dates = [
        'deleted_at'
    ];
    
    protected $refPrefix = 'UCWR-';
    
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            $model->reference = $model->generateReference();
        });
    }
    

    public function getRouteKeyName()
    {
        return 'id';
    }
    
    public function requester()
    {
        return $this->morphTo();
    }
}
