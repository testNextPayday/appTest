<?php

namespace App\Notifications\Users;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use App\Channels\SharenetChannel;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanRequestUpdatedNotification extends Notification
{
    use Queueable;

    public $loanRequest;

    // Who triggered notification
    public $actor = 'Admin';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LoanRequest $loanRequest)
    {
        $this->loanRequest = $loanRequest;
        $this->requestLink = route('users.loan-requests.view', ['reference' => $loanRequest->reference]);

        //
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
            ->subject(config('app.name') . ': Loan  Modification')
            ->line('Your loan request has been  Modified by ' . $this->actor)
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
        $message = 'Your loan request, ' . $this->loanRequest->reference .' was edit';
        $message .= 'You can keep track of this request with the link below: '. PHP_EOL.PHP_EOL;
        $message .= $this->requestLink.PHP_EOL;
        $message .= 'Thank you for using ' . config('app.name');
        return $message;
    }
}
