<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\LoanMandate;

use NumberFormatter;
use App\Models\LoanNote;

use App\Models\Settings;
use App\Helpers\Constants;
use App\Traits\Accounting;
use App\Traits\Settlement;
use App\Models\PenaltyEntry;
use App\Models\UpfrontInterest;
use App\Models\PenaltySetting;
use App\Models\LoanTransaction;
use App\Models\WalletTransaction;
use App\Traits\HasGatewayRecords;
use App\Remita\DDM\MandateManager;
use App\Traits\HasDisbursalAmount;
use App\Traits\LoanPenaltyMethods;
use App\Unicredit\Collection\Utilities;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes, Accounting, 
        ReferenceNumberGenerator,
        Settlement,
        HasGatewayRecords, HasDisbursalAmount, LoanPenaltyMethods;

    protected $refPrefix = 'UC-LN-';

    protected $guarded = [];

    protected $casts = [
        'last_sweep' => 'datetime',
        'due_date' => 'datetime',
        'deleted_at' => 'datetime'

    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->reference = $model->generateReference();
        });

        // depracated

        // self::deleting(function ($model){
            
        //     $loanRequest = $model->loanRequest;
        //     if (! $loanRequest) {
        //         return;
        //     }

        //     $funds = $loanRequest->funds;

        //     if ($funds) {

        //         foreach($funds as $fund){
        //             $investor = $fund->investor;
        //             $investor->refund($fund);
        //         }
        //     }

            
        // });

        // self::restoring(function($model){

        //     $loanRequest = $model->loanRequest;
        //     if (! $loanRequest) {
        //         return;
        //     }

        //     $funds = $loanRequest->funds;

        //     if ($funds) {

        //         foreach($funds as $fund){
        //             $investor = $fund->investor;
        //             $investor->restorefund($fund);
        //         }
        //     }
            
        // });

       
    }

    public function getMonthsLeftAttribute()
    {
        return $this->attributes['monthsLeft'] = $this->repaymentPlans->where('status',0)->count();
    }

    // only restorable if it was deleted after the flagged date in the config
    public function canRestore()
    {
        $deleted_loan_date = config('unicredit.deleted_loans_flag');
        $start_date = Carbon::parse($deleted_loan_date);
        $deleted_date = $this->deleted_at;
        return $start_date->gt($deleted_date) ? false : true;
    }

    /**
     * isUpdatedThisMonth
     *  Checks to see if any of a loans repayment plans has been update for the current month
     * @return bool
     */
    public function isUpdatedThisMonth()
    {
        $cur_month = Carbon::parse('')->format('Y m');
        $uploads = $this->repaymentPlans->filter(function($val,$index) use ($cur_month) {
            return isset($val->date_paid) && Carbon::parse($val->date_paid)->format('Y m') == $cur_month;
        });
      
        return $uploads->count() > 0 ? true : false;
    }
   

    public function scopeActive($query)
    {
        return $query->whereStatus('1')->where('is_managed', false);
    }
    
    public function scopeRemitaActive($query)
    {
        return $query->whereStatus('1')->where('remita_active', true);
    }

    public function scopeFulfilled($query)
    {
        return $query->whereStatus("2");
    }

    // known as defaulting in the database
    public function scopeInActive($query)
    {
        return $query->whereStatus('3');
    }

    public function isActive()
    {
        return $this->status == 1 && $this->is_managed == false && $this->is_penalized == 0;
    }
    
    /**
     *  Checks if a loan has past maturity period
     *
     * @return boolean
     */
    public function isMatured()
    {
        $today = Carbon::today();
        $maturityDate = Carbon::parse($this->due_date);
        return $maturityDate->lt($today);
    }


    public function getMonthsPassedAttribute()
    {
        $today = Carbon::today();
        $date = Carbon::parse($this->created_at);

        return $today->diffInMonths($date);
    }

    public function getEmployerNameAttribute()
    {
        $employment = optional($this->loanRequest)->employment;
        return $employment ? $employment->employer->name : null; 
    }

    public function scopeManaged($query)
    {
        return $query->whereStatus('1')->where('is_managed', true);
    }

    public function scopePenalized($query)
    {
        return $query->whereStatus('1')->where('is_penalized', 1);
    }
    
    /**
     * Active loans with Auto sweep
     *
     * @return void
     */
    public function scopeActiveSweep($query)
    {
        return $query->where('auto_sweeping', 1);
    }

    public function getRouteKeyName()
    {
        return 'reference';
    }

    public function getStatusAttribute($value)
    {
        return $this->is_managed ? 5 : $value;
    }
    
    /**
     * The attribute was created to make change the purpose 
     * of the pause sweep column
     * this name is more descriptive for the current purpose
     * 
     * Determines if a loan shows up on the sweep board
     *
     * @return boolean
     */
    public function getSweepEnabledAttribute()
    {
        return $this->pause_sweep;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function okraSetup()
    {
        return $this->hasMany(OkraSetup::class);
    }

    public function mandates()
    {
        return $this->hasMany(LoanMandate::class);
    }

    public function okraLogs()
    {
        return $this->hasMany(OkraLogs::class);
    }

    public function loanRequest()
    {
        return $this->belongsTo(LoanRequest::class, 'request_id');
    }

    public function transactions()
    {
        return $this->hasMany(LoanTransaction::class);
    }

    
    /**
     * Gets a loan note
     *
     * @return void
     */
    public function notes()
    {
        return $this->hasMany(LoanNote::class);
    }

    /**
     * Returns the bank detail tied to this loan
     * This is especially important for Remita Loans
     */
    public function bank()
    {
        return $this->belongsTo(BankDetail::class, 'bank_id');
    }

    /**
     * Returns the user who collected this loan on behalf of the owner
     * 
     */
    public function collector()
    {
        return $this->morphTo();
    }

    /**
     * Confirms a loan was collected by a supplied personnel
     * 
     * @param $personnel
     * 
     * @return bool
     */

     public function walletTransactions()
     {
         return $this->morphMany(WalletTransaction::class, 'entity');
     }
    public function wasCollectedBy($personnel)
    {
        return $this->collector_type === get_class($personnel) &&
            $this->collector_id === optional($personnel)->id;
    }

    public function closedPayments()
    {
        return $this->repaymentPlans->where('status', 1);
    }

    public function unpaidDueRepayments()
    {
        $today = Carbon::today();
        return $this->repaymentPlans()->where('status', false)->where('payday', '<=', $today)->get();
    }

    public function getClosedPaymentsAttribute()
    {
        return $this->closedPayments();
    }

    public function getUnclosedPaymentsAttribute()
    {
        return $this->unclosedPayments();
    }
    public function unclosedPayments()
    {
        return $this->repaymentPlans->where('status', 0);
    }

    public function repaymentPlans()
    {
        return $this->hasMany(RepaymentPlan::class);
    }
    public function loanReference()
    {
        return $this->hasOne(Loan::class, 'reference', 'top_up_loan_reference');
    }
    
    /**
     * Checks to see if the loan has no collector
     *
     * @return void
     */
    public function noCollector()
    {
        return $this->collector_id == null;
    }

    public function getSuccessFeeAttribute()
    {
        return optional($this->loanRequest)->success_fee;
    }
    public function disbursalAmount()
    {

        // $success_fee = $this->success_fee;
        // $fee = $success_fee > 0 ? $success_fee : (2.5 / 100) * $this->amount;

        // if ($this->loanReference) {
        //     // principal now no longer the emi
        //     $sum = round($this->getEMISum());
            
        //     return ($this->amount - $sum) - $fee;
        // } else {
        //     return $this->amount - $fee;
        // }
        return $this->getDisbursalAmount();
    }
    
    /**
     * Get the amount to be deducted from a new loan during topup
     *
     * @return void
     */
    public function getTopupDeficit()
    {
        return $this->loanReference->is_penalized ? $this->penalizedLoan() : $this->normalLoan();
    }

    public function getEMISum()
    {
        // last repayment balance is now added 
        $ref_loan_plans = $this->loanReference->repaymentPlans;
        $last_repay_balance = $ref_loan_plans->where('status',1)->last()->wallet_balance;

        if ($ref_loan_plans->first()->is_new) {

            $sum = $ref_loan_plans->where('status', 0)->sum('emi');

            if ($last_repay_balance > 0) {

                return $sum - $last_repay_balance;
            } else {

                return $sum + abs($last_repay_balance);
            }
           
        }else{
            
            $emi = $ref_loan_plans->where('status', 0)->sum('emi');

            $mgt_fees = $ref_loan_plans->where('status', 0)->sum('management_fee');
            $sum = $emi + $mgt_fees;
            if ($last_repay_balance > 0) {

                return $sum - $last_repay_balance;
            } else {

                return $sum + abs($last_repay_balance);
            }

            
        }
    }

    public function getUnpaidPrincipal()
    {
        return $this->repaymentPlans->where('status',0)->sum('principal');
    }

    public function canTopUp()
    {
        // if($this->is_penalized) {
        //     return false;
        // }
        
        $total_loans = $this->repaymentPlans->count();

        $total_repaid = $this->repaymentPlans->where('status', 1)->count();
        $total_unpaid = $this->repaymentPlans->where('status', 0)->count();

        if ($total_loans !== 0) {
            $current_repaid = $total_repaid / $total_loans * 100;
        } else {
            return false;
        }

        if ($current_repaid >= 66 && $current_repaid != 100) {
            return true;
        } else {
            return false;
        }
    }


    public function canRestructure()
    {
        return $this->status > 0 && $this->repaymentPlans->count() > 0;
    }

    public function canBeSweptCard()
    {
        return   ($this->status == '1') && ($this->repaymentPlans->count() > 0) && ($this->user->billingCards->count() > 0);
    }

    

   public function loanWalletTransactions()
   {
       return $this->hasMany(LoanWalletTransaction::class);
   }
    
    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }
    
    /**
     * Return restructured length as opposed to 
     *
     * @param  mixed $value
     * @return void
     */
    public function getDurationAttribute($value)
    {
        if ($this->is_restructured) {
            return $this->repaymentPlans->count();
        }

        return $value;
    }

    public function investorUpfrontInterest(){
        return $this->hasOne(UpfrontInterest::class, 'loan_id');
    }


    // by virtue of the new loan restructuring loans emi change will now be changing
    public function emi()
    {
        
        // $duration = $this->created_at->diffInMonths($this->due_date);
        // $interestPercentage = $this->interest_percentage;
        // $rate = $interestPercentage/100;
        // $emi = $this->getFlatEmi($rate, $this->amount, $duration);
        $initialPlan = $this->repaymentPlans->last();
        if(isset($initialPlan) && $initialPlan->is_new){
            return $initialPlan->emi;
        }

        if($this->loanRequest->upfront_interest){
            $duration = $this->duration;
            $amount = $this->amount;
            $emi = $amount/$duration;
            return $emi;
        }
        $emi = $this->monthlyPayment() + $this->mgt_fee();

        return $emi;
    }

    public function mgt_fee()
    {
        return $this->loanRequest->fee($this->amount);
    }


    public function getInterestRate()
    {
        return ($this->interest_percentage / 100);
    }


    public function interest()
    {
        $interest = ($this->monthlyPayment() * $this->duration) - $this->amount;
        return round($interest, 5);
    }

    public function monthlyPayment()
    {
        return $this->pmt(
            $this->amount, 
            $this->interest_percentage, 
            $this->duration
        );
    }


    
    /**
     * Checks if commission has been paid on loan
     *
     * @return bool
     */
    public function commissionPaid()
    {
        $transactions = $this->walletTransactions->where('code', '019');

        return $transactions->count() > 0 ? true : false;
    }



    public function getInterestAttribute()
    {
        //return ($this->emi * $this->duration) - $this->amount;
        return ($this->total_amount - $this->amount);
    }

    public function getPaidAmountAttribute()
    {
        $plans = $this->repaymentPlans->where('status', 1);
        if ($this->repaymentPlans->first()->is_new) {

            return $plans->sum('paid_amount');
        }
        return $plans->sum('emi') + $plans->sum('management_fee');
    }

    public function getTotalAmountAttribute()
    {
        if ($this->repaymentPlans->first()->is_new) {

            return $this->repaymentPlans->sum('emi') + $this->accruedPenalty;
        }
        return $this->repaymentPlans->sum('emi') + $this->repaymentPlans->sum('management_fee') + $this->accruedPenalty;
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - ($this->paid_amount);
        
    }

    public function paymentData()
    {
        $principal = $this->amount;
        $rate = $this->interest_percentage / 100;

        $duration = $this->duration ?? $this->loanRequest->duration;


        $monthly_interest = $this->getInterest($rate, $principal, $duration);
        $monthly_principal = $this->getPrincipal($principal, $duration);

        return [
            'monthly_interest' => $monthly_interest,
            'monthly_principal' => $monthly_principal
        ];
    }

    // QUERY SCOPES


    public function scopeUnfulfilled($query)
    {
        return $query->whereIn('status', ["1", "3"]);
    }

    public function scopeDdmRemita($query)
    {
        return $query->where(
            'collection_methods',
            'like',
            "%code\":\"" . Constants::DDM_REMITA . "%"
        );
    }

    public function scopeDasRemita($query)
    {
        return $query->where(
            'collection_methods',
            'like',
            "%code\":\"" . Constants::DAS_REMITA . "%"
        );
    }

    public function scopeDasIPPIS($query)
    {
        return $query->where(
            'collection_methods',
            'like',
            "%code\":\"" . Constants::DAS_IPPIS . "%"
        );
    }

    /**
     * Filters off unapproved when chained to a collection method query builder
     * @param $query
     */
    public function scopeApproved($query)
    {
        return $query->where(
            'collection_methods',
            'like',
            "%\"status\":2%"
        );
    }

    /**
     * Filters off approved when chained to a collection method query builder
     * @param $query
     */
    public function scopePending($query)
    {
        return $query->where(
            'collection_methods',
            'like',
            "%\"status\":1%"
        );
    }


    public function getMandateUrlAttribute()
    {
        return (new MandateManager)->getMandateUrl($this);
    }

    public function getInsuranceAttribute()
    {
        // can either be fro
        $amount = $this->success_fee > 0 ? $this->success_fee : $this->amount;
        return (2.5 / 100) * $amount;
    }

    /**
     * Updates the status of a specific collection method given its code
     * 
     * @param $code The code of the method to be updated
     * @param $value The value to update to
     * @return bool
     */
    public function updateCollectionMethodStatus($code, $value)
    {
        $collectionMethods = json_decode($this->collection_methods, true) ?? [];
        //dd($collectionMethods);
        foreach ($collectionMethods as $index => $method) {
            if ($method['code'] == $code) {
               
                $collectionMethods[$index]['status'] = $value;

                $this->update(['collection_methods' => json_encode($collectionMethods)]);

                return true;
            }
        }

        // Couldnt find the method
        return false;
    }

    /**
     * Determines whether a loan can be disbursed
     * A loan can be disbursed when its disburse status is less than 4
     * and all the collection methods have a status of 2
     * 
     */
    public function canDisburse()
    {
        $collectionMethods = json_decode($this->collection_methods, true) ?? [];
        foreach ($collectionMethods as $method) {
            if ($method['status'] < 2) {
                return false;
            }
        }

        return true;
    }


    /**
     * Determines whether a loan has a particular collection method
     * The collection method has to be of status 2 too
     * @param String $code [The collection code]
     * @return boolean
     */

    public function hasApprovedCollectionMethod($code)
    {
        $collectionMethods = json_decode($this->collection_methods, true) ?? [];
        foreach ($collectionMethods as $method) {
            if ($method['code'] === $code && $method['status'] == 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns data for creating a Loan's authority form
     * 
     * @return array
     */
    public function getAuthorityFormData($type = 'ippis')
    {
        $loanRequest = $this->loanRequest;
        $employment = $loanRequest->employment;
        $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $startDate = Utilities::getStartDate();

        $endDate = Carbon::createFromFormat('d/m/Y', $startDate);

        return [
            'name' => $this->user->name,
            'bvn' => $this->user->bvn,
            'reg_date' => $this->user->created_at->format('d/m/Y'),
            'date' => $this->created_at->toDateString(),
            'position' => $employment->position,
            'payroll_id' => $employment->payroll_id ?? 'NULL',
            'amount' => $this->amount,
            'amountInWords' => $f->format($this->amount),
            'deductions' => $this->duration,
            'purpose' => $loanRequest->comment,
            'emi' => $this->emi + $loanRequest->fee($this->amount),
            'type' => strtoupper($type),
            'employerName' => $employment->employer->name,
            'startDate' => $startDate,
            'endDate' => $endDate->addMonths($this->duration - 1)->format('d/m/Y')
        ];
    }

    public function getPrimaryCollectionMethodName()
    {
        return config('settings.collection_methods');
    }


    public function getCollectionMethod($type)
    {
        $collectionMethods = json_decode($this->collection_methods) ?? [];

        $code = null;

        foreach ($collectionMethods as $method) {
            if (@$method->type === $type) {
                $code = @$method->code;
                break;
            }
        }

        return $code ? @Constants::generateCollectionCodeMap()[$code] : null;
    }


    public function allTransactions()
    {
        return $this->repaymentPlans->merge($this->transactions);
    }

    public function getTopUpInterest()
    {
        return $this->repaymentPlans->where('status',0)->sum('interest');
    }

    public function getUnpaidRepayments()
    {
       if(optional($this->repaymentPlans->first())->is_new){
           
           return $this->repaymentPlans->where('status', 0)->sum('emi');

       }else{
           
           $emi =  $this->repaymentPlans->where('status',0)->sum('emi') ; 
           $mgt = $this->repaymentPlans->where('status',0)->sum('management_fee');
           return $emi + $mgt;
       }
    }
}
