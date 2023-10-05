<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use App\Channels\SharenetChannel;
use App\Models\WithdrawalRequest;

use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WithdrawalRequestPlacedNotification extends Notification
{
    use Queueable;
    
    private $withdrawalRequest;

    private $requestLink;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(WithdrawalRequest $withdrawalRequest)
    {
        $this->withdrawalRequest = $withdrawalRequest;
        $this->requestLink = route('users.withdrawals.index');
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
                    ->subject(config('app.name') . ': New Withdrawal Request')
                    ->line('You withdrawal request for ₦' . $this->withdrawalRequest->amount . 'has been received')
                    ->action('View Withdrawal Requests', $this->requestLink)
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
        $message = 'You withdrawal request for ₦' . $this->withdrawalRequest->amount . 'has been received'.PHP_EOL;
        $message .= 'Use this link to view your withdrawal rquests:'. PHP_EOL;
        $message .= $this->requestLink.PHP_EOL;
        $message .= 'Thank you for using ' . config('app.name');
        
        return $message;
    }
}
