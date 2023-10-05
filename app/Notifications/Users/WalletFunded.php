<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WalletFunded extends Notification
{
    use Queueable;

    private $amount;
    
    private $code;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($amount,$code)
    {
        $this->amount = $amount;
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting("Hello, " . $notifiable->name)
                    ->line('Your wallet has been credited ₦ ' . number_format($this->amount, 2) . ' for reference ' . $this->code . '.')
                    ->action('Visit Account', url('/'))
                    ->line('Thank you for choosing '. config('app.name') .'!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
