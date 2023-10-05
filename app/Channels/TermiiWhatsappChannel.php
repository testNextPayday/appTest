<?php
namespace App\Channels;


use App\Repositories\WhatsappInterface;
use Illuminate\Notifications\Notification;

class TermiiWhatsappChannel

{

    private $whatsappHandler;
    
    public function __construct(WhatsappInterface $whatsappHandler)
    {
        $this->whatsappHandler = $whatsappHandler;
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
        $message = $notification->toTermiiWhatsapp($notifiable);

        // Send notification to the $notifiable instance...
        $this->whatsappHandler->whatsappNotify(make_smsable($notifiable->phone),$message);
    }

}
?>