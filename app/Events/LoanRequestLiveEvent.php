<?php

namespace App\Events;

use App\Models\LoanRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LoanRequestLiveEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $loanRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LoanRequest $loanRequest)
    {
        //
        $this->loanRequest = $loanRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
