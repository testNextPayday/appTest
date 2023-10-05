<?php

namespace App\Notifications\Users;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanRepaymentNotification extends Notification
{
    use Queueable;

    private $loan;
    private $unpaidAmount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Loan $loan,$unpaidAmount)
    {
        $this->loan = $loan;
        $this->unpaidAmount = $unpaidAmount;
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
                    ->subject(config('app.name') . 'Loan Repayment Notification')
                    ->line("Dear {$notifiable->name}, ")
                    ->line('This is to inform you that your '.$this->loan->duration.' months loan collected on '.$this->loan->created_at.' is over due for repayment.')
                    ->line('The total pending amount is : '. $this->unpaidAmount)
                    ->action('Kindly click this link to pay up', url('https://paystack.com/pay/ftaunokjrp'))
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
        $msg = "Dear $notifiable->name, your ".$this->loan->duration." months loan collected on ".$this->loan->created_at." is over due for repayment.".PHP_EOL;
        $msg .= "The total pending amount is : ". $this->unpaidAmount;
        $msg .= "Kindly click this link to pay up', url('https://paystack.com/pay/ftaunokjrp)";
        $msg .= "Thank you for using ". config('app.name') . "!";
        return $msg;
    }
}
