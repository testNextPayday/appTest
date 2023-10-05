<?php

namespace App\Notifications\Investors;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Loan;

class FundsDisbursedNotification extends Notification
{
    use Queueable;

    private $loan;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
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
                    ->line($notifiable->name)
                    ->line("This is to inform you that your funds for request, " .$this->loan->loanRequest->reference ." have been disbursed.")
                    ->line('Amount: ₦'.number_format($this->loan->amount,2))
                    ->line('Interest Rate: '.$this->loan->interest_percentage.'% PM')
                    ->line('Tenure :'. $this->loan->duration.' months')
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
}
