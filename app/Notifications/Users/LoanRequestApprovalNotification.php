<?php

namespace App\Notifications\Users;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use App\Channels\SharenetChannel;

use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanRequestApprovalNotification extends Notification
{
    use Queueable;
    
    private $loanRequest;
    
    /** Approval verdict */
    private $action;
    
    /** Verdict giver */
    private $actor;
    
    private $requestLink;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LoanRequest $loanRequest)
    {
        $this->loanRequest = $loanRequest;
        $this->requestLink = route('users.loan-requests.view', ['reference' => $loanRequest->reference]);
        switch($loanRequest->status) {
            case 1:
                $this->action = 'Approved';
                $this->actor = 'your Employer/Supervisor';
                break;
            case 5:
                $this->action = 'Declined';
                $this->actor = 'your Employer/Supervisor';
            case 7 : 
                $this->action = 'Referred';
                $this->actor = 'Admin';
                break;
            case 6:
            default:
                $this->action = 'Declined';
                $this->actor = 'Admin';
        }
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
                    ->subject(config('app.name') . ': Loan ' . $this->action)
                    ->line('Your loan request has been ' . $this->action . ' by ' . $this->actor)
                    ->line($this->loanRequest->status == 6 ? 'Reason:'.$this->loanRequest->decline_reason.'' : '')
                    ->line($this->loanRequest->status == 7 ? 'Reason:'.$this->loanRequest->decline_reason.'' : '')
                    ->action('View Request', $this->requestLink)
                    ->line('Thank you for using ' . config('app.name'));
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
    
    
    public function toTermii($notifiable)
    {
        $message = 'Your loan request has been ' . $this->action . ' by ' . $this->actor;
        $message .= $this->loanRequest->status == 6 ?  'Reason:'.$this->loanRequest->decline_reason.'' : ''.PHP_EOL;
        $message .= $this->loanRequest->status == 7 ?  'Reason:'.$this->loanRequest->decline_reason.'' : ''.PHP_EOL;
        $message .= 'You can view your loan request with this link:' . PHP_EOL;
        $message .= $this->requestLink;
        $message .= 'Thank you for using ' . config('app.name');
        
        return $message;
    }
}
