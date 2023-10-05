<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Channels\SharenetChannel;
use App\Models\LoanRequest;

class LoanRequestPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $loanRequest;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LoanRequest $loanRequest)
    {
        $this->loanRequest = $loanRequest;
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
        
        $disbursed = $this->loanRequest->amount - $this->loanRequest->success_fee;
        return (new MailMessage)
                    ->subject(config('app.name'). ': New Loan Request Placed')
                    ->greeting('Hello, ' . $notifiable->name)
                    ->line('You just placed a loan request on ' . config('app.name') .' with reference, ' . $this->loanRequest->reference)
                    ->line('Here is a breakdown of your loan request details:')
                    ->line('Basic information:')
                    ->line('Name: ' . $notifiable->name)
                    ->line('Loan Information')
                    ->line('Request Amount: ' .$this->loanRequest->amount)
                    ->line('Amount to be disbursed: ' . $disbursed)
                    ->line('Success Fee: ' . $this->loanRequest->success_fee)
                    ->line('Loan Tenure: ' . $this->loanRequest->duration)
                    ->line('Monthly Repayment: ' . (! $this->loanRequest->using_armotization) ? $this->loanRequest->emi() + $this->loanRequest->managementFee() : $this->loanRequest->emi())
                    ->action('View Loan Request', route('users.loan-requests.view', ['reference' => $this->loanRequest->reference]))
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
        $message = 'You just placed a loan request on our platform'. PHP_EOL;
        $message .= 'You can monitor this request using this link: '. PHP_EOL;
        $message .= route('users.loan-requests.view', ['reference' => $this->loanRequest->reference]); 
    
        return $message;
    }
}
