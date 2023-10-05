<?php
namespace App\Helpers;

use App\Models\Repayment;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;



class ReversePayment
{

    
    /**
     * Reverses a payment made to the investor
     *
     * @param  mixed $payment
     * @return void
     */
    public static function reverse(Repayment $payment)
    {
        $investor = $payment->investor;

        $code = config('unicredit.flow')['corrective_rvsl'];

        $financeHandler = new FinanceHandler(new TransactionLogger);

        $financeHandler->handleSingle(
            $investor,
            'debit',
            $payment->amount,
            $payment,
            'V',
            $code
        );

        // Commission Reversal
        if ($payment->commission) {

            $financeHandler->handleSingle(
                $investor,
                'credit',
                $payment->commission,
                $payment,
                'V',
                $code
            );
    
        }
       
        // Tax payment made
        if ($payment->tax) {

            $financeHandler->handleSingle(
                $investor,
                'credit',
                $payment->tax,
                $payment,
                'V',
                $code
            );
        }
       

        $payment->update(['reversed'=> 1]);

    }
}