<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\LoanRequest;

class LoanRequestApprovalRequestNotification extends Notification
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
        $loanRequest = $this->loanRequest;
        
        $urlApproveRequest =  route('employers.loan-requests.approve', ['code' => $loanRequest->acceptance_code, 'reference' => $loanRequest->reference]);
        $urlDeclineRequest = route('employers.loan-requests.decline-form', ['code' => $loanRequest->acceptance_code, 'reference' => $loanRequest->reference]);
        
        return (new MailMessage)
                    ->subject('Authorization Needed')
                    ->markdown('mail.LoanRequestApprovalRequestNotification', ['loanRequest' => $loanRequest, 'urlDeclineRequest' => $urlDeclineRequest, 'urlApproveRequest' => $urlApproveRequest]);
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
