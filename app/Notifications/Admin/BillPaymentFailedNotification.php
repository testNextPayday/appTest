<?php

namespace App\Notifications\Admin;

use Exception;
use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Unicredit\Exceptions\ReadableException;
use Illuminate\Notifications\Messages\MailMessage;

class BillPaymentFailedNotification extends Notification
{
    use Queueable;

    protected $bill;

    protected $exception;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Bill $bill, Exception $exception)
    {
        //
        $this->bill = $bill;
        $this->exception = new ReadableException($exception);
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
                    ->subject('Failed Automated Bill Payment')
                    ->greeting('Good day Sir,')
                    ->error()
                    ->line('An automated bill payment has failed on the application')
                    ->line('Name: '. $this->bill->name)
                    ->line('Amount: '.number_format($this->bill->amount, 2))
                    ->line('Frequency: '.$this->bill->occurs)
                    ->line($this->exception->getErrorMessage())
                    ->line('Thank you for using our application!');
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
