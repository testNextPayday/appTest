<?php

namespace App\Notifications\Users;

use App\Models\Bid;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use App\Channels\SharenetChannel;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BidCancelledNotification extends Notification
{
    use Queueable;
    
    private $bid;
    
    private $bidder;
    
    private $loanFund;
    
    private $loanOwner;
    
    private $bidLink;
    
    private $loanLink;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Bid $bid)
    {
        $this->bid = $bid;
        $this->bidder = $bid->user;
        $this->loanFund = $bid->loanFund;
        $this->loanOwner = $this->loanFund->lender;
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
        $mailMessage = (new MailMessage)->subject(config('app.name'). ': Bid Cancelled');
        
        if($notifiable->id === $this->bidder->id) {
            $message = $mailMessage->line('Hello, your bid on ' . $this->loanFund->reference . ' has been cancelled')
                    ->action('View your bids', $this->bidLink)
                    ->line('Thank you for using ' . config('app.name'));
        } else {
            $message = $mailMessage->line('Hello, you just cancelled a bid on ' . $this->loanFund->reference)
                    ->action('View Loan', $this->loanLink)
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
        if($notifiable->id === $this->bidder->id) {
            $message = 'Hello, your bid on ' . $this->loanFund->reference . ' has been cancelled';
            $message .= 'View your bids with the link below:' . PHP_EOL.PHP_EOL;
            $message .= $this->bidLink;
            $message .= 'Thank you for using ' . config('app.name');
            
        } else {
            $message = 'Hello, you just cancelled a bid on ' . $this->loanFund->reference;
            $message .= 'View your loan with the link below:' . PHP_EOL.PHP_EOL;
            $message .= $this->loanLink . PHP_EOL;
            $message .= 'Thank you for using ' . config('app.name');
        }
        
        return $message;
    }
}
