<?php

namespace App\Models;

use App\Models\GatewayTransaction;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $guarded = [];


    public function getRouteKeyName()
    {
        return 'id';
    }
    
    public function owner()
    {
        return $this->morphTo();
    }


    public function gatewayRecords()
    {
        return $this->morphMany(GatewayTransaction::class,"owner");
    }
}
