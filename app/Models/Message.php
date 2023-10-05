<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];
    
    
    protected $with = ['sender', 'receiver'];
    
    public function sender()
    {
        return $this->morphTo('sender');
    }
    
    public function receiver()
    {
        return $this->morphTo('receiver');
    }
}
