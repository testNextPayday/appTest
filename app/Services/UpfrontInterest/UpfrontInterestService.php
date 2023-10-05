<?php
namespace App\Services\UpfrontInterest;

use App\Models\UpfrontInterest;
use Illuminate\Support\Facades\Auth;
use App\Traits\Accounting;

class UpfrontInterestService
{

    protected $loanRequest;
    use Accounting;
    
    /**
     * Create Upfront Interest Record For A Loan Request
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public  function create($loanRequest, $request){

        $this->loanRequest = $loanRequest;

        return UpfrontInterest::create([
            'request_id' => $loanRequest->id,
            'user_id' => Auth::id(),
            'interest'=> $request->interest_sum,
            'mgt_fee' => $request->total_mgt_fee,
            'loan_fee' => $request->charge,
            'total_payment' => $request->upfront_charge
        ]);        
    }

    public function getManagementFee()
    {
        $loanRequest = $this->loanRequest;
        $employer = $loanRequest->employment->employer;
        $feePercentage = $loanRequest->getMgtForDuration();        
        $mgtFee = $loanRequest->amount * $feePercentage/100;
        $total_mgt_fee = $mgtFee * $employer->duration; 
        return $total_mgt_fee;      
    } 

    public function getInterestSum(){
        $loanRequest = $this->loanRequest;
        $employer = $loanRequest->employment->employer;        
        $percentage = 0.5;
        $duration = $loanRequest->duration;
        $begin_bal =$loanRequest->amount;
        $rate = $percentage/100;
        $monthly_payments = $this->pmt($begin_bal, $rate, $duration);
        $interestSum = 0;
            if ($employer) {                        
                if($loanRequest->upfront_interest) {                          
                    while($duration > 0) {
                        $begin_bal = isset($end_balance) ? $end_balance : $begin_bal;                    
                        $interest =  round($rate * $begin_bal, 5);
                        $principalPart = round($monthly_payments - $interest, 5);
                        $end_balance = round($begin_bal - $principalPart,5);
                        $duration = $duration - 1;
                        $interestSum += $interest;
                    }                           
                }
            }     
        $interest_sum = $interestSum; 
        return $interest_sum;
    }
    
    public function upfrontCharge(){
        $loanRequest = $this->loanRequest;
        $employer = $loanRequest->employment->employer;
        if($employer){
            $loanFee = $loanRequest->charge;
            $upfront_interest = $this->getManagementFee + $this->getInterestSum + $loanFee;
            return $upfront_interest;
        }        
     }
}