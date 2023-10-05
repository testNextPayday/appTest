<?php

namespace App\Notifications\Users;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use App\Channels\TermiiChannel;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LoanSetupNotification extends Notification
{
    use Queueable;

   
    public $loan;

    protected $setupLink;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Loan $loan)
    {
        //
        $this->loan = $loan;

        // Generate a temporary setup link
        $this->setupLink = URL::temporarySignedRoute('users.loan.setup', now()->addHours(24), ['loan' => $this->loan->reference]);

        $this->dashboardLink = route('users.loan.setup-dashboard', ['loan'=>$this->loan->reference]);

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TermiiChannel::class, 'database'];
        //return ['mail', TermiiChannel::class, 'database'];

    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     
    public function toMail($notifiable)
    {
        return (new MailMessage)

            ->line('Your loan with reference number : '.$this->loan->reference.' has been scheduled ')

            ->line('Please click the link below to help setup your collection method')

            ->action('Setup Collection', $this->setupLink)

            ->line('This link is only valid for 24 hours')

            ->line('Thank you for using our application!');

    }*/


     /**
     * Get the termii representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toTermii($notifiable)
    {

        $message = 'Your loan with reference number : '.$this->loan->reference.' is requesting setup'.PHP_EOL;

        $message .= 'Please click the link below to help setup your collection method'.PHP_EOL;

        $message .=$this->setupLink.PHP_EOL;

        $message .="This link is only available for 24 hours".PHP_EOL;

        $message .= 'Thank you for using ' . config('app.name');

        return $message;
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [

            'link'=>$this->dashboardLink,
            'title'=>'Loan Setup Notification',
            'desc'=> 'Setup your loan '.$this->loan->reference.' ('.number_format($this->loan->amount, 2).')',
            'meta'=> ['loan'=>$this->loan]
        ];
    }
}
