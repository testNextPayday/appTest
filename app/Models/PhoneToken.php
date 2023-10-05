<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ReferenceNumberGenerator;

class PhoneToken extends Model
{
    //
    use ReferenceNumberGenerator;

    protected $refPrefix = '';
    
    protected $guarded = [];

    public function generateToken($model)
    {
        $token = $this->generateKey(null, 6);
        $query = $model ? $model::whereToken($token) : self::whereToken($token);
        while ($query->count() > 0) {
            $token = $this->generateKey(null, 6);
        }

        return $token;
    }

    public static function boot()
    {
        parent::boot();
        
        self::creating(function($model) {
            $model->token = $model->generateToken($model);
        });
    }
    
}
