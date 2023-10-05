<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $body;
    protected $sub;
    protected $sender;
    public function __construct($body,$sub,$sender)
    {
        //
        $this->body = $body;
        $this->sub = $sub;
        $this->sender = $sender;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender)->subject($this->sub)->markdown('mail.custom')->with(['body' => $this->body]);
    }
}
