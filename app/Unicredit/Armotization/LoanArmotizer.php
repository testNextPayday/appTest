<?php
namespace App\Unicredit\Armotization;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\RepaymentPlan;
use App\Unicredit\Collection\Utilities;
use App\Unicredit\Contracts\LoanArmotizerStrategy;

class LoanArmotizer

{
    private $armotizerStrategy;

    public function __construct(LoanArmotizerStrategy $armotizerStrategy = null)
    {
      $this->armotizerStrategy = $armotizerStrategy;
    }


    public function armotize($params)
    {
        $this->armotizerStrategy->setupArmotizedRepayments($params);
    }


    
}
?>