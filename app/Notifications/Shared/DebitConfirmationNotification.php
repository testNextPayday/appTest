<?php

namespace App\Notifications\Shared;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\RepaymentPlan;

class DebitConfirmationNotification extends Notification
{
    use Queueable;

    /**
     * @var plan
     */
    private $plan;
    
    /**
     * @var loan
     */
    private $loan;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    
    public function __construct(RepaymentPlan $plan)
    {
        $this->plan = $plan;
        $this->loan = optional($plan->loan);
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
                    ->subject("Loan Recovery Notification")
                    ->line("₦{$plan->amount} has been recovered for loan, {$loan->reference}.")
                    ->line("Recovery Date: {$plan->date_paid}")
                    ->line("RRR: {$plan->rrr}");
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
