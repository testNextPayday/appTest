<?php 
namespace App\Unicredit\Armotization;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\RepaymentPlan;
use App\Structures\Restructuringload;
use App\Unicredit\Collection\Utilities;
use App\Unicredit\Contracts\LoanArmotizerStrategy;
use UnexpectedValueException;

class RestructuringLoanArmotizer implements LoanArmotizerStrategy
{

    protected $paidCounter;

    protected $paidPlans;

    protected function armotizePaid()
    {
        $loanAmount = $this->loan->amount;

        foreach ($this->paidPlans as $plan) {

            // the plan is armotised already just skipp and continue
            if ($plan->is_new) {
                continue;
            }
            $emi = $plan->interest + $plan->principal + $plan->management_fee;
            $payments = $emi - $plan->management_fee;
            $principal = $payments - $plan->interest;

            // because payment amounts are the same 
            // if the plan has a elder sibling get balance of elder sibling else its the first so give loan  amount
            $beginBalance = $plan->prevSibling() ? $plan->prevSibling()->balance : $this->loan->amount;
            $endBalance = $beginBalance - $principal;

            $plan->update(
                [
                'principal'=>$principal,
                'balance'=>$endBalance,
                'begin_balance'=>$beginBalance,
                'payments'=> $payments,
                'emi'=>$emi,
                'is_new'=>true,
                 ]
            );
            
        }
            
            
    }
    protected function setPaidCounter()
    {
        $this->paidPlans = $this->loan->repaymentPlans->where('status',true);
        $this->paidCounter = $this->paidPlans->count();
        $this->armotizePaid();
    }

    protected function clearOutPendingRepaymentPlans()
    {
        return $this->loan->repaymentPlans->where('status',false)->each(function($item,$index){
            return $item->forceDelete();
        });
    }

    protected function initiateArmotization()
    {
       
        $monthNumber = $this->paidCounter;

        $begin_bal = $this->loan->getUnpaidPrincipal() + $this->addedAmount + $this->charge;

        $rate = $this->loan->interest_percentage;

        $duration = $this->duration;
       
        $monthly_payments = pmt($begin_bal, $rate, $duration);
       
        //the management fee of other plans
        $firstPlan = $this->loan->repaymentPlans->first();
        $managementFee = $firstPlan ? $firstPlan->management_fee : $this->loan->mgt_fee() ;
        $emi = round($monthly_payments +  $managementFee, 2);

        $loan_period = $this->loan->loanRequest->loan_period;
        
        $interest_rate = ($rate)/100;
        while($duration > 0 ) {

            $begin_bal = isset($end_balance) ? $end_balance : $begin_bal;
            $interest = round($interest_rate * $begin_bal,5);
            $principalPart = round($monthly_payments - $interest,5);
            $end_balance = round($begin_bal - $principalPart,5);

            $lastUnPaidPlan = $this->loan->repaymentPlans->where('status', 1)->first();

            if ($lastUnPaidPlan) {

                $startDate = Carbon::parse($lastUnPaidPlan->payday);

            }else {

                $startDate = Carbon::createFromFormat('d/m/Y', Utilities::getStartDate());
            }
            
            
            
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
                'payday' => ($loan_period == 'monthly') ? $startDate->addMonths($monthNumber)->format('Y-m-d') : $startDate->addWeeks($monthNumber)->format('Y-m-d'),
                'month_no' => ++$monthNumber, 
                'status' => false
            ]);
            $duration = $duration - 1;
        }
        
        return true;
    }

    
    public function setupArmotizedRepayments($load)
    {
        
        $this->loan = $load['loan'];

        $this->charge = $load['charge'];

        $this->addedAmount = $load['addedAmount'];

        $this->setPaidCounter();

        $this->duration = $load['duration'];
      
        $loanDuration  = $this->duration + $this->paidCounter;
      
        $this->loan->update(['duration'=>$loanDuration, 'is_restructured'=>true]);
        $this->loan->refresh();
        
        // delete the ones we have not paid
        $this->clearOutPendingRepaymentPlans();

        $this->initiateArmotization();

        //Refresh Loans
        $loan = $this->loan;
        $loan->refresh();
        $lastRepayment = $loan->repaymentPlans->last();
        $emi  = $lastRepayment->total_amount;
        $dueDate = $lastRepayment->payday;

        $this->loan->update(['emi'=> $emi, 'due_date'=>$dueDate]);

        return true;
       
    } 
}
?>