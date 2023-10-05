<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use App\Models\LoanWalletTransaction;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LoanWalletTransactionApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trnx;

    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LoanWalletTransaction $trnx, $data)
    {
        //
        $this->trnx = $trnx;

        $this->data = $data;
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
