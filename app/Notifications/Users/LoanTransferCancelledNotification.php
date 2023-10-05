<?php

namespace App\Notifications\Users;

use App\Models\LoanFund;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use App\Channels\SharenetChannel;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanTransferCancelledNotification extends Notification
{
    use Queueable;
    
    private $loanFund;
    
    private $loanOwner;
    
    private $bidLink;
    
    private $loanLink;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LoanFund $loanFund)
    {
        $this->loanFund = $loanFund;
        $this->loanOwner = $loanFund->lender;
        
        $this->bidLink = route('lenders.bids.index');
        
        $this->loanLink = route('users.loans.given.view', ['id' => encrypt($this->loanFund->id)]);
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
        $mailMessage = (new MailMessage)->subject(config('app.name') . ': Loan Transfer Cancelled');
        
        if($notifiable->id === $this->loanOwner->id) {
            $message = $mailMessage->line('Hello, you just cancelled your loan transfer request for ' . $this->loanFund->reference)
                    ->action('View Loan', $this->loanLink)
                    ->line('Thank you for using ' . config('app.name'));
                    
        } else {
            $message = $mailMessage->line('Hello, your bid on ' . $this->loanFund->reference . ' has been cancelled and your wallet refunded')
                    ->action('View your bids', $this->bidLink)
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
    
    public function toTermii($notifiable)
    {
        if($notifiable->id === $this->loanOwner->id) {
            $message = 'Hello, you just cancelled your transfer request for ' . $this->loanFund->reference;
            $message .= 'View your loan with the link below:' . PHP_EOL.PHP_EOL;
            $message .= $this->loanLink . PHP_EOL;
            $message .= 'Thank you for using ' . config('app.name');
        } else {
            $message = 'Hello, your bid on ' . $this->loanFund->reference . ' has been cancelled and your wallet refunded';
            $message .= 'View your bids with the link below:' . PHP_EOL.PHP_EOL;
            $message .= $this->bidLink;
            $message .= 'Thank you for using ' . config('app.name');
        }
        
        return $message;
    }
}
