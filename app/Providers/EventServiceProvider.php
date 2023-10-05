<?php

namespace App\Providers;

use App\Events\SendOTPEvent;

use App\Events\LoanDisbursedEvent;
use App\Events\RefundApprovedEvent;
use App\Events\InvestorsUpfrontInterestEvent;
use App\Events\LoanRequestLiveEvent;
use App\Listeners\HandleOTPListener;
use Illuminate\Support\Facades\Event;

use App\Listeners\RefundApprovedListener;
use App\Listeners\InvestorsUpfrontInterestListener;
use App\Events\PromissoryNoteCreatedEvent;
use App\Events\PaystackCustomerRefundEvent;

use App\Listeners\AutomaticLoanFundListener;
use App\Listeners\FundDisbursedLoanListener;
use App\Events\LoanWalletTransactionApproved;
use App\Events\Investor\InvestorWalletFundEvent;
use App\Listeners\ConfirmPlanFromWalletListener;
use App\Listeners\PromissoryNoteCreatedListener;
use App\Listeners\PaystackCustomerRefundListener;
use App\Listeners\Investor\InvestorWalletFundListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

       InvestorWalletFundEvent::class => [
            InvestorWalletFundListener::class
        ],

        PromissoryNoteCreatedEvent::class => [
            PromissoryNoteCreatedListener::class
        ],

       LoanRequestLiveEvent::class => [
           AutomaticLoanFundListener::class
       ],
       
       SendOTPEvent::class => [
           HandleOTPListener::class
       ],

       LoanDisbursedEvent::class => [
           FundDisbursedLoanListener::class
       ],

       LoanWalletTransactionApproved::class => [
           ConfirmPlanFromWalletListener::class
       ],

       RefundApprovedEvent::class => [

            RefundApprovedListener::class
       ],

       PaystackCustomerRefundEvent::class => [
           PaystackCustomerRefundListener::class
       ],

       InvestorsUpfrontInterestEvent::class => [
        InvestorsUpfrontInterestListener::class
    ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
