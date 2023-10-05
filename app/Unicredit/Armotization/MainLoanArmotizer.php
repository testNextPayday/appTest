<?php 
namespace App\Unicredit\Armotization;

use App\Models\Loan;
use App\Unicredit\Contracts\LoanArmotizerStrategy;

class MainLoanArmotizer implements LoanArmotizerStrategy
{


    public function setupArmotizedRepayments(Loan $loan)
    {
        $this->loan = $loan;
        
        $borrower = $this->loan->user;
        
        //activate all loan funds
        $loanRequest = $this->loan->loanRequest;
        $loanRequest->funds()->update(['status' => 2]);
       
        $monthNumber = 0;
        $monthly_payments = $this->loan->monthlyPayment();
        $begin_bal = $this->loan->amount;
        $managementFee = $this->loan->mgt_fee();
        // Now using success fees no more management fees
        
        $duration = $this->loan->duration;
        $emi = $this->loan->emi();
        $interest_rate = ($this->loan->interest_percentage)/100;
        
       
        
        while($duration > 0) {

            $begin_bal = isset($end_balance) ? $end_balance : $begin_bal;
            $interest = round($interest_rate * $begin_bal,5);
            $principalPart = round($monthly_payments - $interest,5);
            $end_balance = round($begin_bal - $principalPart,5);
            
            $startDate = Carbon::createFromFormat('d/m/Y', Utilities::getStartDate());
            
            RepaymentPlan::create([
                'loan_id' => $this->loan->id, 
                'interest' => $interest, 
                'principal' => $principalPart,
                'emi' => $emi,
                'management_fee' => $managementFee, 
                'balance' => $end_balance, 
                'begin_balance'=>$begin_bal,
                'payments'=>$monthly_payments,
                'is_new'=>true,
                'payday' => $startDate->addMonths($monthNumber)->format('Y-m-d'),
                'month_no' => ++$monthNumber, 
                'status' => false
            ]);
            $duration = $duration - 1;
        }
        
        return true;
    } 
}
?>