<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanRestructureNotification extends Notification
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
                    ->line('Your loan with reference number '.$this->loan->reference.' has been restructured to give you a better value')
                    ->line(' Your new EMI : '.$loan->emi());

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toTermii($notifiable)
    {
        $loan  = $this->loan;
        $emi = $loan->emi();
        $msg = "Dear $notifiable->name, ".PHP_EOL;
        $msg.= "Your loan with reference number $loan->reference has been restructured to give you a better value. ";
        $msg.= "Your new EMI : $emi. ".PHP_EOL;
        $msg .= 'Thank you for using ' . config('app.name');

        return $msg;
    }
}
