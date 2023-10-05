<?php
namespace App\Services\Investor;

use App\Services\Investor\PromissoryPaymentService;

/**
 *  The value of the investment does not grow, You only get recurrent monthly payments
 */

class MonthlyPromissoryService extends PromissoryPaymentService
{

   /**
     * Pays Interest to the current value and not for withdrawal
     * 
     * @return bool
     */
    public function payInterest()
    {
        
        // Get the specific interest
        $rate = $this->promissoryNote->rate;
        $tenure = $this->promissoryNote->tenure;
        $amount = $this->promissoryNote->principal;
        $taxRate = $this->promissoryNote->tax;

        $interest = ((($amount * $rate/100)/12) * $tenure)/ $tenure;

        // Deduct tax from interest
         $tax = ($taxRate/100) * $interest;
         $taxCode = config('unicredit.flow')['tax_payment'];
         $this->toPromissoryLog($tax, 'debit', $taxCode);

        $payableInterest = $interest - $tax;

        $interestCode = config('unicredit.flow')['fund_recovery'];
        $this->toPromissoryLog($payableInterest, 'debit', $interestCode);

       
         // Create withdrawal for 
         $this->createWithdrawals($payableInterest);

    }
}