<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupNotification extends Model
{
    //
    protected $guarded = [];

    
    /**
     * Unread Ticket Message
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeUnattendTickets($query)
    {
        return $query->where('type', 'ticket')->where('read', 0);
    }

    
    /**
     * The entity being notified about
     *
     * @return void
     */
    public function entity()
    {
        return $this->morphTo();
    }
}

