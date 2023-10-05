<?php

namespace App\Notifications\Users;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanDisbursementNotification extends Notification
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
                    ->line("Dear $notifiable->name, your loan has been disbursed to your account successfully.")
                    ->line('Did you know that you can refer your friends to get loan from us and earn up to 1.25%of the disbursed amount?. Hurry now and get your friends involved')
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
        $msg = "Dear $notifiable->name, your loan has been disbursed to your account successfully.".PHP_EOL;
        $msg .= "Did you know that you can refer your friends to get loan from us and earn up to 1.25%of the disbursed amount?.";
        $msg .=" Hurry now and get your friends involved";
        return $msg;
    }
}
