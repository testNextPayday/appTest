<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use App\Models\Loan;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SalaryPercentageDowngradeNotification extends Notification
{
    use Queueable;

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
        return (new MailMessage)
            ->subject(config('app.name'). ': Loan Plan Downgrade')
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Your last fulfilled loan repayment was unconfirmed and you have been downgraded to a '.config('settings.salary_plans')[$notifiable->salary_percentage]. 
            ' Loan Plan. You can only access '.$notifiable->salary_percentage.'% of your Salary on '. config('app.name2'). '.')
            ->line('Thank you for using '. config('app.name2') .'!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toTermii($notifiable) {
        $message = 'Dear, ' . $notifiable->name.' Your Last fulfilled Loan Repayment was Unconfirmed and you have been downgraded to a '.config('settings.salary_plans')[$notifiable->salary_percentage]. 
        ' Loan Plan.  You can only access '.$this->salary_percentage.'% of your salary'. PHP_EOL;        
        $message .= 'Thank you for using ' . config('app.name2');
        $message .= route('users.loan-requests.new');    
        return $message;
    }
}
