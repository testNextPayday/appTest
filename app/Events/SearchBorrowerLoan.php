<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class SearchBorrowerLoan implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $borrowers;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($borrowers)
    {
        //
        $this->borrowers = $borrowers;
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel|array
     */
    public function broadcastOn()
    {
       
        return new PrivateChannel('searchBorrower');
    }


    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'borrowerResults';
    }

    // public function broadcastAs()
    // {
    //     return 'borrowerResults';
    // }
}
