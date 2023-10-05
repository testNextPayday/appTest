<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerification extends Notification
{
    use Queueable;

    private $code;
    
    private $email;
    
    private $guard;

    private $user;
    
    private $verificationLink;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $code, $email, $guard = 'web')
    {   
        $this->name  = $name;
        $this->code  = $code;
        $this->email = $email;
        $this->guard = $guard;
        $this->verificationLink = route('email.verify', [
            'code' => $code, 'email' => $email, 'guard' => $guard
        ]);
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
                    ->subject(config('app.name') . ': Email Activation')
                    ->greeting('Hello, '. $this->name)
                    ->line('Thank you for signing up with '. config('app.name') .'. Please click the button below to verify your email')
                    ->action('Verify Email', $this->verificationLink)
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
