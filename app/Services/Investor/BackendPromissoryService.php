<?php
namespace App\Services\Investor;

use App\Services\Investor\PromissoryPaymentService;

/**
 * This service the current value and payable value grows to the maturity value
 */
class BackendPromissoryService extends PromissoryPaymentService
{

    
    public function payInterest():void
    {
        // Get the specific interest
        $rate = $this->promissoryNote->rate;
        $tenure = $this->promissoryNote->tenure;
        $amount = $this->promissoryNote->principal;
        $taxRate = $this->promissoryNote->tax;

        $interest = round(((($amount * $rate/100)/12) * $tenure)/ $tenure, 2);
        // Deduct tax from interest
       
        $tax = round(($taxRate/100) * $interest, 2);
       
        $taxCode = config('unicredit.flow')['tax_payment'];
        $this->toPromissoryLog($tax, 'debit', $taxCode);

        $payableInterest = $interest - $tax;
        $interestCode = config('unicredit.flow')['fund_recovery'];
        $this->toPromissoryLog($payableInterest, 'credit', $interestCode);

        // Update to being paid interest
         $currentValue = $this->promissoryNote->current_value +  $payableInterest;
         $this->promissoryNote->update(['current_value'=> $currentValue]);
         $this->promissoryNote->update(['payable_value'=> $currentValue]);

        

    }
    
    
}