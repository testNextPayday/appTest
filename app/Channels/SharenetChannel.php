<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

use App\Repositories\SmsRepository;

class SharenetChannel
{
    private $smsHandler;
    
    public function __construct(SmsRepository $smsHandler)
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
        $message = $notification->toSharenet($notifiable);

        // Send notification to the $notifiable instance...
        $this->smsHandler->send($notifiable->phone, config('unicredit.company_name'), $message);
    }
}