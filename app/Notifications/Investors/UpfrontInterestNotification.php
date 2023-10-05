<?php

namespace App\Notifications\Investors;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Loan;

class UpfrontInterestNotification extends Notification
{
    use Queueable;


    public $loan;
    public $loanFund;
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
                    ->line('Dear '. $notifiable->name.' this is to notify you that the upfront interest on a loan you funded has been paid today '.now())
                    ->line('All principal on this loan would be paid as due.')                   
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
        $msg = "Dear $notifiable->name,the upfront intrerest on a loan you funded has been paid today ".now().PHP_EOL;
        $msg .= "All principal on this loan would be paid as due.";
        $msg .="Thank you for using our application!";
        return $msg;
    }
}
