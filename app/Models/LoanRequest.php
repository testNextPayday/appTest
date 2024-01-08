<?php

namespace App\Models;

use Storage;
use Carbon\Carbon;
use Keygen\Keygen;
use App\Traits\Accounting;
use App\Traits\HasDisbursalAmount;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRequest extends Model
{
    use SoftDeletes, Accounting, ReferenceNumberGenerator, HasDisbursalAmount;
    
    protected $guarded = [];

    protected $dates = [
        'expected_withdrawal_date', 'acceptance_expiry', 'deleted_at'
    ];
    
    
    public function getRouteKeyName()
    {
        return 'reference';
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repayment(){

        return $this->hasMany(Repayment::class, 'loan_id', 'id');
    }
    
    public function loan()
    {
        return $this->hasOne(Loan::class, 'request_id');
    }
    
    public function funds()
    {
        return $this->hasMany(LoanFund::class, 'request_id');
    }

    public function investorUpfrontInterest()
    {
        return $this->hasOne(UpfrontInterest::class, 'request_id');
    }
    
    public function scopeAvailable($query)
    {
        return $query->whereStatus(2)
            ->where('percentage_left', '>', 0)
            ->where('mandateStage', 0);
    }
    
    /**
     * Get all the loanRequest that have not been assigned
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeUnassigned($query)
    {
        return $query->whereStatus(4)->where('placer_id', null)->where('placer_type', '0');
    }
    
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
    
    public function investors()
    {
        $ids = $this->funds()->get()->pluck('investor_id');
        return Investor::whereIn('id', $ids)->get();
    }    
    
    /**
     * Returns the personnel that placed this loan request
     */
    // public function placer()
    // {
    //     return $this->morphTo('placer');
    // }    
    
    /**
     * Confirms a loan request was placed by a supplied personnel
     * 
     * @param $personnel
     * 
     * @return bool
     */
    public function wasPlacedBy($personnel)
    {
        return $this->placer_type === get_class($personnel)
            && $this->placer_id === optional($personnel)->id;
    }
    
    public function employment()
    {
        return $this->belongsTo(Employment::class);
    }
    
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }    
    
    public function funders()
    {
        $funds = $this->funds()->get()->pluck('lender_id');
        return User::whereIn('id', $funds)->get();
    }
    
    public function potentialEmi()
    {
        if($this->upfront_interest){
            $duration = $this->duration;
            $amount = $this->amount;
            $emi = $amount/$duration;
            return $emi;
        }
        if ($this->using_armotization) {
            
            $emi = $this->monthlyPayment($this->amount) + $this->mgt_fee();
           
        } else {
            $duration = $this->duration;
            $interestPercentage = $this->interest_percentage;
            $rate = $interestPercentage/100;
            $emi = $this->getFlatEmi($rate, $this->amount, $duration);
        }
        
        return $emi;
    }

    public function mgt_fee()
    {
        return $this->fee($this->amount);
    } 
    
    public function getMgtForDuration()
    {
        $employer = optional($this->employment)->employer;
        $mgt = 0;
        if($employer){
            if ($this->duration <= 3) {
                $mgt =  $employer->fees_3;            
            }elseif($this->duration > 3 && $this->duration <= 6) {               
                $mgt = $employer->fees_6;
            }else {
                $mgt = $employer->fees_12;;
            }
        } 
        return $mgt;
    }

    public function monthlyPayment($amount)
    {
        if($this->upfront_interest){
            $duration = $this->duration;
            $loanAmount = $this->amount;
            $monthlyPayment = $loanAmount/$duration;
            return $monthlyPayment;
        }

        return $this->pmt($amount, $this->interest_percentage, $this->duration);
    }
    
    public function currentEmi(){
        if($this->upfront_interest){
            $duration = $this->duration;
            $amount = $this->amount;
            $emi = $amount/$duration;
            return $emi;
        }            
        if($this->using_armotization) {         
            return $this->monthlyPayment($this->amountRealized) + $this->mgt_fee();            
        }else{
            $duration = $this->duration;
            $interestPercentage = $this->interest_percentage;
            $rate = $interestPercentage/100;
            return $this->getFlatEmi($rate, $this->amountRealized, $duration);
        }
    }
    
    public function emi(){
        $loan = $this->loan;
        if (!$loan) return $this->potentialEmi();
        return $loan->emi();        
    }
    
    public function getUsingArmotizationAttribute(){
        $new_loan_date_flag = config('unicredit.new_loans_flag');
        $start_date = Carbon::parse($new_loan_date_flag);
        $loan_request_date = Carbon::parse($this->created_at);
        return $start_date->lt($loan_request_date);
    }
    
    public function managementFee(){
        return $this->amount * (Settings::managementFee() /100);
    }
    
    public function fee($amount = false){
        return  $this->calculateManagementFee($amount);        
    }

    private function calculateManagementFee($amount)
    {
        $employment = $this->employment;
        
        if (!$employment) return $this->managementFee();
        
        $employer = $employment->employer;
        
        if (!$employer) return $this->managementFee();
        
        if ($this->duration <= 3) {
            $feePercentage = $employer->fees_3;
        } else if ($this->duration > 3 && $this->duration <= 6) {
            $feePercentage = $employer->fees_6;
        } else {
            $feePercentage = $employer->fees_12;
        }
        
        $requestAmount = $amount ?: $this->amount;
        
        return $requestAmount * ($feePercentage / 100);
    }
    
    // Attributes    
    public function getBankStatementAttribute($statement)
    {
        return preg_match('/^http/', $statement) ? $statement : asset(Storage::url($statement));
    }
    
    public function getPaySlipAttribute($slip){
        return asset(Storage::url($slip));
    }
    
    public function getCollectionPlanAttribute($plan) 
    {
        return @config('unicredit.collection_plans')[$plan];
    }
    
    public function getAmountRealizedAttribute()
    {
        return $this->amount;
        //return $this->amount - ($this->percentage_left  * $this->amount / 100);
    }

    public function disbursalAmount()
    {
        return $this->getDisbursalAmount();

        // $success_fee = $this->success_fee;
        // $fee = $success_fee > 0 ? $success_fee : (2.5 / 100) * $this->amount;

        // if ($this->loanReference) {
        //     // principal now no longer the emi
        //     $sum = round($this->getEMISum());
            
            
        //     return ($this->amount - $sum) - $fee;
        // } else {
        //     return $this->amount - $fee;
        // }
    }

    public function getEMISum(){
        // last repayment balance is now added 
        $ref_loan_plans = $this->loanReference->repaymentPlans;
        $last_repay_balance = $ref_loan_plans->where('status', 1)->last()->wallet_balance;

        if($ref_loan_plans->first()->is_new){
            $sum = $ref_loan_plans->where('status', 0)->sum('emi');
            if ($last_repay_balance > 0) {
                return $sum - $last_repay_balance;
            } else {
                return $sum + abs($last_repay_balance);
            }
        }else{
            $emi = $ref_loan_plans->where('status',0)->sum('emi');
            $mgt_fees = $ref_loan_plans->where('status',0)->sum('management_fee');
            $sum = $emi + $mgt_fees;
            if ($last_repay_balance > 0) {

                return $sum - $last_repay_balance;
            } else {

                return $sum + abs($last_repay_balance);
            }
        }
    }

    public function getLoanReferenceAttribute()
    {
        return Loan::whereReference($this->loan_referenced)->first();
    }
}
