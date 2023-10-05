<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ReferenceNumberGenerator;

class BorrowerGroup extends Model
{
    use ReferenceNumberGenerator;
    protected $guarded = [];

    protected $refPrefix = 'NP-GRP-';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->reference = $model->generateReference();
        });
    }

}
