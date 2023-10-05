<?php

namespace App\Models;

use App\Models\GroupNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketMessage extends Model
{
    //

    protected $guarded = [];

    
    /**
     * Gets the ticket a message belongs to
     *
     * @return void
     */
    public function ticket()
    {
        return $this->belongsTo(\App\Models\Ticket::class);
    }

    
    /**
     * Gets the user that sent the message
     *
     * @return void
     */
    public function owner()
    {
        return $this->morphTo();
    }

    
    /**
     * Get files attached to a conversation
     *
     * @param  mixed $files
     * @return void
     */
    public function getFilesAttribute($files)
    {
        return !$files ? null : asset(Storage::url(json_decode($files)));
    }

    
    /**
     * Gets the notification sent out for this 
     *
     * @return void
     */
    public function notification()
    {
        return $this->morphOne(GroupNotification::class, 'entity');
    }
}
