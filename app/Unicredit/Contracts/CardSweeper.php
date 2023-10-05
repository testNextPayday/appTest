<?php
namespace App\Unicredit\Contracts;

use App\Models\User;
use App\Models\BillingCard;
use App\Models\RepaymentPlan;


interface CardSweeper

{

    public function attemptInBits(RepaymentPlan $plan);

  
}
?>