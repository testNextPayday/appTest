<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Loan;

class LoanAcceptedNotification extends Notification
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
        $insurance = (2.5/100) * $this->loan->amount;
        $disbursed = $this->loan->amount - $insurance;
        return (new MailMessage)
                    ->greeting('Hello, ' . $notifiable->name)
                    ->line('Congrats on finalizing your loan collection process for ' . $this->loan->reference)
                    ->line('Here is a breakdown of your loan details:')
                    ->line('Basic information:')
                    ->line('Name: ' . $notifiable->name)
                    ->line('Loan Information')
                    ->line('Request Amount: ' .$this->loan->loanRequest->amount)
                    ->line('Realized Amount: ' . $this->loan->amount)
                    ->line('Disbursed Amount: ' . $disbursed)
                    ->line('Insurance: ' . $insurance)
                    ->line('Loan Tenure: ' . $this->loan->duration)
                    ->line('Monthly Repayment: ' . $this->loan->emi())
                    ->line('Please head over to your withdrawals page and request for cash transfer for this loan')
                    ->action('View Loan', route('users.loans.view', ['reference' => $this->loan->reference]))
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
