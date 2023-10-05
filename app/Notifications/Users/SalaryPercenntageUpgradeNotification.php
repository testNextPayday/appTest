<?php

namespace App\Notifications\Users;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SalaryPercenntageUpgradeNotification extends Notification
{
    use Queueable;
    
    /**
     * user
     *
     * @var \App\Models\User
     */
    private $user;


    private $loan;
    
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
                    ->subject(config('app.name'). ': Loan Plan Upgrade')
                    ->greeting('Hello, ' . $notifiable->name)
                    ->line('Due to your consistency in making Repayment in due time, you have been upgraded to a '.config('settings.salary_plans')[$notifiable->salary_percentage]. ' Loan Plan')
                    ->line('You can now access '. $notifiable->salary_percentage.'% of your Salary on '. config('app.name2'). '.')
                    ->line('Thank you for using '. config('app.name2') .'!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    

    public function toTermii($notifiable) {
        $message = 'Dear, ' . $notifiable->name.' Your Loan has been fulfilled and you have been upgraded to '.config('settings.salary_plans')[$notifiable->salary_percentage]. ' Loan Plan.  You can now access'.$this->salary_percentage.'% of your salary'. PHP_EOL;        
        $message .= 'Thank you for using ' . config('app.name2');
        $message .= route('users.loan-requests.new');    
        return $message;
    }
}
