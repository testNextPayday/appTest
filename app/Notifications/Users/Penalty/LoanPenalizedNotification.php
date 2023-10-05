<?php

namespace App\Notifications\Users\Penalty;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanPenalizedNotification extends Notification
{
    use Queueable;

    protected $loan;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        //
        $this->loan = $loan;
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
            ->line("Dear {$notifiable->name}, ")
            ->line('This is to inform you that your loan has now been penalized and would incur further penalty')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toTermii($notifiable)
    {
        $msg = "Dear $notifiable->name,".PHP_EOL;
        $msg .= "This is to inform you that your loan has now been penalized and would incur further penalty";
        $msg .="Thank you for using nextpayday";
        return $msg;
    }
}
