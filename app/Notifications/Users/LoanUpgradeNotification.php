<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Loan;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\SharenetChannel;

class LoanUpgradeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
        $this->salary_percentage = $loan->user->salary_percentage;
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
                    ->subject(config('app.name'). ': Loan Request Upgrade')
                    ->greeting('Hello, ' . $notifiable->name)
                    ->line('You can now access '. $this->salary_percentage.'% of your Salary on'. config('app.name'). '.')                    
                    ->action('Book Loan', route('users.loan-requests.new'))
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
        $message = 'Hello, ' . $notifiable->name.' You can now access'.$this->salary_percentage.'% of your salary'. PHP_EOL;        
        $message .= route('users.loan-requests.new');    
        return $message;
    }
}
