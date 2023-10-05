<?php
namespace App\Channels;

use App\Repositories\SmsInterface;
use Illuminate\Notifications\Notification;

class TermiiChannel

{

    private $smsHandler;
    
    public function __construct(SmsInterface $smsHandler)
    {
        $this->smsHandler = $smsHandler;
    }
    
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTermii($notifiable);

        // Send notification to the $notifiable instance...
        $this->smsHandler->sendSMS(make_smsable($notifiable->phone), $message);
    }

}
?>