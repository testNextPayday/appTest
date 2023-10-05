<?php

use Illuminate\Support\Str;
use App\Helpers\InvestorAffiliateFees;
use App\Unicredit\Collection\DetermineRepayment;

function create($class, $attributes = [], $times = null)
{
    return factory($class, $times)->create($attributes);
}

function make($class, $attributes = [], $times = null)
{
    return factory($class, $times)->make($attributes);
}

function refresh_vault($investor,$plan)
{
    $loanFund = $investor->loanFunds->first();
    $loan = $plan->loan;
    $determinePayment = new DetermineRepayment($investor, $plan);

    $fundFraction = $loanFund->amount / $loan->amount;
   
    $currentTotal = $determinePayment->getCurrentTotal();
    $investorsCut =  $fundFraction * $currentTotal;
    
    $interestFraction = $fundFraction * $determinePayment->getTotalInterest();

    $commission = $interestFraction * ($investor->commission_rate / 100);
    $tax = $interestFraction * ($investor->tax_rate / 100);

    $fullRate = InvestorAffiliateFees::getFullRate($loan);

    $monthlyCharge = (($fullRate/100) * $loanFund->amount) / $loan->duration;
    
    $vaultIncrement = $investorsCut - ($tax + $monthlyCharge + $commission);
    
    return $vaultIncrement;
}
