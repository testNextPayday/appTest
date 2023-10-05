<?php

namespace App\Notifications\Users;

use App\Models\LoanFund;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use App\Channels\SharenetChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanFundedNotification extends Notification
{
    use Queueable;
    
    private $loanFund;
    
    private $fundLink;
    
    private $requestLink;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LoanFund $loanFund)
    {
        $this->loanFund = $loanFund;
        
        $this->fundLink = route('investors.funds.show', ['reference' => $loanFund->reference]);
        
        $this->requestLink = route('users.loan-requests.view', ['reference' => $loanFund->loanRequest->reference]);
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
        $mailMessage = (new MailMessage)->subject(config('app.name') . ': Loan Funded');
        
        if($notifiable->id === $this->loanFund->investor_id) {
            $message = $mailMessage->line('You just funded ' . 
                                            $this->loanFund->percentage . '% (₦' .
                                            number_format($this->loanFund->amount, 2) . ') of a loan on ' . config('app.name'))
                                ->action('View Funding', $this->fundLink)
                                ->line('Thank you for using ' . config('app.name'));
        } else {
            $message = $mailMessage->line('You just received ' . 
                                            $this->loanFund->percentage . '% (₦' .
                                            number_format($this->loanFund->amount, 2) . ') of your loan request on ' . config('app.name'))
                                ->action('View Loan Request', $this->requestLink)
                                ->line('Thank you for using ' . config('app.name'));
        }
        return $message;
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
        if($notifiable->id === $this->loanFund->investor_id) {
            $message = 'You just funded ' . $this->loanFund->percentage . '% (NGN ' . number_format($this->loanFund->amount, 2) . ') of a loan on ' . config('app.name');
            $message .= PHP_EOL. 'Click the link below to view this funding';
            $message .= PHP_EOL. $this->fundLink;
            $message .= PHP_EOL. 'Thank you for using ' . config('app.name');
        } else {
            $message = 'You just received ' . $this->loanFund->percentage . '% (NGN ' . number_format($this->loanFund->amount, 2) . ') of your loan request on ' . config('app.name');
            $message .= PHP_EOL. 'Click the link below to view this funding';
            $message .= PHP_EOL. $this->requestLink;
            $message .= PHP_EOL. 'Thank you for using ' . config('app.name');
        }
        return $message;
    }
}
