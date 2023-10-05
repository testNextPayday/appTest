<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvestmentMade extends Mailable
{
    use Queueable, SerializesModels;

    private $certificateLink;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($certificateLink)
    {
        $this->certificateLink = $certificateLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.investment_made', [
            'data' => 'sample'
        ])->attach($this->certificateLink, [
            'as' => 'certificate.pdf',
            'mime' => 'application/pdf',
        ]);
    }
}
