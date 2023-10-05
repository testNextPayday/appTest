<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\User;
use App\Models\RepaymentPlan;

class PaymentDue extends Mailable
{
    use Queueable, SerializesModels;

    public $invoiceLink;
    
    public $user;
    
    public $plan;
    
    public $invoiceUrl;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoiceLink, User $user, RepaymentPlan $plan)
    {
        $this->invoiceLink = $invoiceLink;
        $this->user = $user;
        $this->plan = $plan;
        
        $loan = $plan->loan;
        $this->invoiceUrl = route('users.loans.view', ['reference' => $loan->reference]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.invoice_due', [
                        'user' => $this->user,
                        'invoiceUrl' => $this->invoiceUrl
                    ])
                    ->attach($this->invoiceLink, [
                        'as' => 'invoice.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
