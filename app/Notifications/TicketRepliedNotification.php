<?php

namespace App\Notifications;

use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TicketRepliedNotification extends Notification
{
    use Queueable;

    public $message;

    public $ticket;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TicketMessage $message)
    {
        //
        $this->message = $message;

        $this->ticket = $message->ticket;
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
                    ->line('The introduction to the not')
                    ->action('View Ticket', route('users.ticket.show', ['ticket'=> $this->ticket->reference]))
                    ->line('Thank you for using our application!');
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
