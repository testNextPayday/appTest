<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use App\Channels\SharenetChannel;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
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
                    ->subject(config('app.name') . ': Welcome')
                    ->line('Welcome to '. config('app.name') .'! We are glad to have you here.')
                    ->line('On '. config('app.name') .', you can apply for loans in a quick and effective way. You also have wonderful opportunities to make money when you upgrade to a lender status.')
                    ->line('Get started by completing your profile')
                    ->action('Complete Profile', route('users.profile.index'))
                    ->line('Thank you for using '. config('app.name') .'!');
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
    
    public function toTermii($notifiable) {
        $message = 'Welcome to '. config('app.name') .'! We are glad to have you here'. PHP_EOL;
        $message .= 'On '. config('app.name') .', you can apply for loans in a quick and effective way.'. PHP_EOL;
        $message .= 'You also have wonderful opportunities to make money when you upgrade to a lender status.'. PHP_EOL;
    
        return $message;
    }
}
