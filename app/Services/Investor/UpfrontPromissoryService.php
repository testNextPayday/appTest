<?php
namespace App\Services\Investor;

use App\Services\Investor\PromissoryPaymentService;

class UpfrontPromissoryService extends PromissoryPaymentService
{
    
    /**
     * Pays Interest to the current value and not for withdrawal
     * No tax again since we took all tax upfront
     * @return bool
     */
    public function payInterest()
    {
        
        // Get the specific interest
        $rate = $this->promissoryNote->rate;
        $tenure = $this->promissoryNote->tenure;
        $amount = $this->promissoryNote->principal;

        $interest = ((($amount * $rate/100)/12) * $tenure)/ $tenure;

        // Increase current value by interest paid
        $currentValue = $this->promissoryNote->current_value + $interest;
        $this->promissoryNote->update(
            ['current_value'=> $currentValue, 'payable_value'=>$currentValue]
        );
        
        $interestCode = config('unicredit.flow')['fund_recovery'];

        $this->toPromissoryLog($interest, 'credit', $interestCode);

    }

    
    /**
     * Initial upfront payment on creation of the note
     *
     * @return void
     */
    public function upfrontInterest()
    {
        // Get the specific interest
        $rate = $this->promissoryNote->rate;
        $tenure = $this->promissoryNote->tenure;
        $amount = $this->promissoryNote->principal;
        $taxRate = $this->promissoryNote->tax;

        $interest = ((($amount * $rate/100)/12) * $tenure);

        // Update Current Value and Maturity Value
        $currentValue = $this->promissoryNote->principal - $interest;
        $this->promissoryNote->update(
            ['current_value'=> $currentValue, 'payable_value'=>$currentValue]
        );

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