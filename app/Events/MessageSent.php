<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\Message;
use App\Models\Admin;
use App\Models\Affiliate;
use App\Helpers\Constants;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        switch($this->message->receiver_type) {
            case Admin::class:
                $code = Constants::ADMIN_CODE;
                break;
            case Affiliate::class:
                $code = Constants::AFFILIATE_CODE;
                break;
            default:
                $code = "";
        }
        
        return new PrivateChannel("conversations.{$code}.{$this->message->receiver_id}");
    }
}
