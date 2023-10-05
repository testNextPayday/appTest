<?php

namespace App\Notifications\Staff;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InviteNotification extends Notification
{
    use Queueable;

    private $email;
    
    private $code;
    
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($email, $code)
    {
        $this->email = $email;
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
                    ->subject(config('app.name') . ': Staff Invite')
                    ->line('You\'ve been invited to become a staff on '. config('app.name') .'.')
                    ->line('Please click the link below to complete your registration process')
                    ->action('Complete Registration', route('staff.invitation.register', ['email'=> $this->email, 'code' => $this->code]))
                    ->line('NB: This link expires in 24 Hours')
                    ->line('Thank you!');
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
