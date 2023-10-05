<?php

namespace App\Models;

use App\Models\TicketMessage;
use App\Models\GroupNotification;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Relations\Relation;

class Ticket extends Model
{
    //
    use ReferenceNumberGenerator;

    protected $guarded = [];


    protected $refPrefix = 'NPD-TK-';


    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->reference = $model->generateReference();
        });

    }

    
    /**
     * Gets the route key bind column
     *
     * @return void
     */
    public function getRouteKeyName()
    {
        return 'reference';
    }

  

    
    /**
     * Gets the owner of the this ticket
     *
     * @return void
     */
    public function owner()
    {
        return $this->morphTo();
    }

    
    /**
     * Gets all messages pertaining to this ticket
     *
     * @return void
     */
    public function messages()
    {
        return $this->hasMany(\App\Models\TicketMessage::class);
    }

    
    /**
     * All tickets awaiting staff reply
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeAwaitingStaffReply($query)
    {
        return $query->where('status', 1);
    }

    
    /**
     * Get all notifications on this ticket
     *
     * @return void
     */
    public function notifications()
    {
        $messages = $this->messages;

        $collection = collect();

        foreach ($messages as $message) {

            $collection->push($message->notification);
        }

        return $collection;
    }


    
    /**
     * Gets the status of the ticket in human readable
     *
     * @param  mixed $value
     * @return void
     */
    public function getStatusTextAttribute()
    {
        $status = '';

        switch ($this->status) {

            case 1: 
                $status = 'Awaiting Staff Reply';
            break;

            case 2 : 
                $status = 'Awaiting Customer Reply';
            break;

            case 3 :
            default :
                $status = 'Closed';
        }

        return $status;
    }
}
