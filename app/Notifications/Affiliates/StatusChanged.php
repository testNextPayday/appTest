<?php

namespace App\Notifications\Affiliates;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Action Carried out on account
     * verified, activated, deactivated
     * 
     */
    public $action;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($action)
    {
        $this->action = $action;
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
        $message = (new MailMessage)
                        ->subject(config('app.name') . " Account " . ucfirst($this->action))
                        ->greeting("Hello");
        
        if ($this->action === "verified") {
            $message = $message->line("Your application as a next pay day affiliate is successful.")
                            ->line("Kindly log on to your dash board and update your profile.")
                            ->line("You can also visit our Knowledge base for more information on our products.");
                                
        } else {
            
            $message = $message->line("This is to inform you that your account has been $this->action");
        }   
        
        
        if ($this->action !== "deactivated") {
            $message = $message->action('See Dashboard', route('affiliates.dashboard'));
        }
        
        return $message->line('Thank you for using ' . config('app.name'));
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
