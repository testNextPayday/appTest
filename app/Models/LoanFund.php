<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Accounting;
use App\Traits\ReferenceNumberGenerator;
use Carbon\Carbon;

class LoanFund extends Model
{
    use SoftDeletes, Accounting, ReferenceNumberGenerator;
    
    protected $guarded = [];
    
    protected $refPrefix = 'UC-LF-';
    
    protected $dates = [
        'transfer_date'    
    ];
    
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            $model->reference = $model->generateReference();
        });
    }
    
    public function getRouteKeyName()
    {
        return 'reference';
    }
    
    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class, 'request_id');
    }

    public function repayment()
    {
        return $this->hasMany(Repayment::class, 'fund_id');
    }
    
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
    
    public function bids()
    {
        return $this->hasMany(Bid::class, 'fund_id');
    }
    
    public function scopeOnSale($query)
    {
        $query->where('status', 4);    
    }

    public function scopeSold($query)
    {
        return $query->where('status', 5)->whereNotNull('transfer_date');
    }
    
    public function scopeAcquired($query)
    {
        $query->whereNotNull('original_id');
    }

    public function scopeActive($query)
    {
        $query->whereStatus('2');
    }
    
    public function scopeChildFund($query, $id = null)
    {
        $id = $id ?? $this->id;
        $query->where('original_id', $id);
    }
    
    public function getCurrentValueAttribute()
    {
        // $emi = $this->loanRequest->monthlyPayment($this->loanRequest->amount);
        // $value = $this->timeLeft * $emi;
        $value = $this->loanRequest->loan ?  $this->outstandingPayments()['principal'] : $this->amount;
        
        return round($value, 2);
    }
    public function getOutstandingInterestAttribute()
    {
        $value = $this->outstandingPayments()['interest'];
        return round($value, 2);
    }

    public function getPotentialGainAttribute(){
       
        $profit =  $this->outstandingPayments()['interest'];
        return number_format($profit, 2);
    }

    public function outstandingPayments()
    {
        $outstanding['principal'] = 0;
        $outstanding['interest'] = 0; 
        $loan = $this->loanRequest->loan; 
       
        $plans = $loan->repaymentPlans->where('paid_out', 0);
       
        foreach($plans as $plan){
            $fundFraction = ($this->amount/$loan->amount);
          
            $investor_principal =  $plan->principal * $fundFraction;
           
            $investor_interest = $fundFraction * $plan->interest;
            $outstanding['principal'] += $investor_principal;
            $outstanding['interest'] += $investor_interest;
        }

        return $outstanding;
         
    }

    public function outstandingInterest()
    {

    }
    
    public function getTimeLeftAttribute()
    {
        $now = Carbon::now();
        if ($loan = $this->loanRequest->loan) {
            return $loan->due_date->diffInMonths($now);
        }
        
        return $this->loanRequest
                    ->expected_withdrawal_date->diffInMonths($now);
    }
    
    public function getBiddersAttribute()
    {
        $bids = $this->bids->pluck('investor_id');
        return Investor::whereIn('id', $bids)->get();
    }
    
    public function original()
    {
        return $this->belongsTo(LoanFund::class, 'original_id');
    }
    
    
    public function lender()
    {
        return $this->belongsTo(User::class, 'lender_id');
    }
    
    public function potentialEmi()
    {
        $loanRequest = $this->loanRequest;
        $requestEMI = $loanRequest->currentEmi();
        $fundPercentage = $this->amount / $loanRequest->amount;
        return $fundPercentage * $requestEMI; //subtract commission rate
    }
    
    public function emi()
    {
        $loanRequest = $this->loanRequest;
        $loan = $loanRequest->loan;
        
        if ($loan) {
            $fundFraction = $this->amount / $loan->amount;
            $commissionRate = @($this->investor->commission_rate / 100) ?? 0;
            
            $loanData = $loan->paymentData();
            $investorPrincipal = $loanData['monthly_principal'] * $fundFraction;
            $investorInterest = $loanData['monthly_interest'] * $fundFraction;
            $commission = $investorInterest * $commissionRate;
            
            return $investorInterest + $investorPrincipal - $commission;
        }
        
        return $this->potentialEmi();
    }


    // i wrote this function to check the difference between the new armotized loan
    // in which management fee in now contained in the emi and the old one where emi and 
    // management fee were different 
    public function newEmi()
    {
        $new_loan_date = config('unicredit.new_loans_flag');
        $start_date = Carbon::parse($new_loan_date);
        $loan_request_date = $this->created_at;
        return $start_date->gt($loan_request_date) ? $this->emi() : $this->loanRequest->monthlyPayment($this->amount);
      
    }
}
