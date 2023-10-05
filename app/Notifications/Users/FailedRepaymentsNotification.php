<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use App\Channels\TermiiWhatsappChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FailedRepaymentsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($plan)
    {
        //
        $this->loan = optional($plan)->loan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TermiiWhatsappChannel::class];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the termiwhatsapp  representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toTermiiWhatsapp($notifiable)
    {
        $message = 'Your monthly repayment for loan , ' . $this->loan->reference .' was not successful. '.PHP_EOL;
        $message .= " Amount : ₦".number_format($this->plan->emi).PHP_EOL;
        $message .= 'You should fund your account for the next active sweep so as to get it paid';
        $message .= 'Thank you for using ' . config('app.name');
        return $message;
    }
}
