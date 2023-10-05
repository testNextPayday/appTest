<?php

namespace App\Notifications\Affiliates;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Meeting;

class MeetingScheduleUpdated extends Notification
{
    use Queueable;

    private $meeting;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Meeting $meeting)
    {
        $this->meeting = $meeting;
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
        $firstLine = 'This is to inform you that your meeting schedule has been changed.';
        return (new MailMessage)
                    ->subject(config('app.name') . ": Meeting Schedule Updated")
                    ->greeting('Hello')
                    ->line($firstLine)
                    ->line('See below the updated details of this meeting:')
                    ->line('Venue: ' . $this->meeting->venue . "\r\n")
                    ->line('Time: ' . $this->meeting->when->format('Y-m-d h:i A') . "\r\n")
                    ->line('State: ' . $this->meeting->state . "\r\n")
                    ->line($this->meeting->requirements ? 'Requirements: ' . $this->meeting->requirements . "\r\n" : '')
                    ->line($this->meeting->extras ? 'Extra Information: ' . $this->meeting->extras . "\r\n" : '')
                    ->line('We are sorry for any inconvenience. Thank you!');
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
