<?php

namespace App\Notifications\Users\Penalty;

use App\Models\Loan;
use App\Models\PenaltySweep;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanPenaltyCardSweepNotification extends Notification
{
    use Queueable;

    protected $loan;

    protected $sweep;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Loan $loan, PenaltySweep $sweep)
    {
        //
        $this->loan  = $loan;
        $this->sweep = $sweep;
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
                    ->line("This is to inform you that there has been successful penalty charge of {$this->sweep->amount} on your account")
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the termii representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toTermii($notifiable)
    {
        $msg = "Dear $notifiable->name,".PHP_EOL;
        $msg .= "This is to inform you that there has been successful penalty charge of {$this->sweep->amount} on your account";
        $msg .="Thank you for using nextpayday";
        return $msg;
    }
}
