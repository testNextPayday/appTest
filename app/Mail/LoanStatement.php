<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoanStatement extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($loan_statement_pdf,$loan_reference_no)
    {
        //
        $this->loan_statement_pdf = $loan_statement_pdf;
        $this->loan_reference_no = $loan_reference_no;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.loan_statement', [
            'data' => $this->loan_reference_no
        ])->attach($this->loan_statement_pdf, [
            'as' => 'loan_statement.pdf',
            'mime' => 'application/pdf',
        ]);
    }
}
