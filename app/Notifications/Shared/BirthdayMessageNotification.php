<?php

namespace App\Notifications\Shared;

use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BirthdayMessageNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', TermiiChannel::class];
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
                    ->line('From your friends at Nextpayday, we are extremely excited for you on reaching such a milestone in your life.')
                    ->line("We hope all the days of your life will be as great as you are to us.")
                    ->line('Happy birthday '.$notifiable->name.' !')
                    ->line('Thank you for using being a part of us! ');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toTermii($notifiable)
    {

        $message = "From your friends at Nextpayday, ";
        $message .="we are extremely excited for you on reaching such a milestone in your life. ";
        $message .="We hope all the days of your life will be as great as you are to us. ";
        $message .="Happy birthday ".$notifiable->name." !";

        return $message;
    }
}
