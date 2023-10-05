<?php

namespace App\Events\Investor;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InvestorWalletFundEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $investor;

    public $amount;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($investor, $amount)
    {
        //
        $this->investor = $investor;

        $this->amount  = $amount;
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
