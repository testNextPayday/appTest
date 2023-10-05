<?php

namespace App\Models;

use Storage;
use App\Models\Bid;
use App\Models\LoanFund;
use App\Models\Settings;

use App\Models\Repayment;
use App\Models\BankDetail;
use App\Models\KeyOfficer;
use App\Models\LoanRequest;
use App\Traits\ReadableModel;
use App\Helpers\FinanceHandler;
use App\Relations\EmptyRelation;
use App\Traits\InvestorAccounts;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Helpers\TransactionLogger;
use Laravel\Passport\HasApiTokens;
use App\Traits\ReferenceNumberGenerator;
use Illuminate\Notifications\Notifiable;
use App\Models\InvestorVerificationRequest;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\Investors\ResetPasswordNotification;

class Investor extends Authenticatable
{
    use Notifiable, ReferenceNumberGenerator, HasApiTokens, InvestorAccounts, ReadableModel;

    protected $guarded = [];

    protected $refPrefix = 'NP-IN-';

    protected $hidden = [
        'password'
    ];

    protected $cast = [
        'employer_loan'=>'array'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->reference = $model->generateReference();
        });
    }


    public function isPremium()
    {
        return $this->role == 1;
    }


    public function isPromissory()
    {
        return $this->role == 2;
    }


    public function referrer()
    {
       
        return $this->morphTo('adder');
    }


    public function promissoryNotes()
    {
        return $this->hasMany(\App\Models\PromissoryNote::class);
    }


    public function promissoryTransaction()
    {
        return $this->hasMany(\App\Models\PromissoryNoteTransaction::class);
    }


    public function promissoryWithdrawals()
    {
        $this->withdrawals->where('status', 2)->sum('amount');
    }


    public function promissoryPortfolioSize()
    {
        $activeNotes = $this->promissoryNotes->where('status', 1)->sum('payable_value');

        return $activeNotes + $this->wallet;
    }


    public function promissoryTax()
    {
        $code = config('unicredit.flow')['tax_payment'];

        $sum = $this->promissoryTransaction->where('code', $code)->sum('amount');

        return $sum;
    }


    public function promissoryIncomeEarned()
    {
        $code = config('unicredit.flow')['fund_recovery'];

        $sum = $this->promissoryTransaction->where('code', $code)->sum('amount');

        return $sum;
    }


    public function getReferrer()
    {
        if ($this->adder_type == 'AppModelsInvestor' || $this->adder_type == null) {

            return optional();
        }

        return $this->referrer;
    }

    
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopePromissoryNote($query)
    {
        return $query->where('role', 2);
    }

    public function getRouteKeyName()
    {
        return 'reference';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function loanFunds()
    {
        return $this->hasMany(LoanFund::class);
    }

    public function loanFundsQuery()
    {
        $loan_fund_ids = $this->loanFunds->pluck('id')->toArray();
        return LoanFund::whereIn('id', $loan_fund_ids);
    }

    public function tickets()
    {
        return $this->morphMany(\App\Models\Ticket::class, 'owner');
    }

    public function loanRequests()
    {
        return $this->hasMany(LoanRequest::class);
    }

    public function scopeAcquiredFunds($query)
    {
        return $this->loanFunds()->whereNotNull('original_id');
    }

    public function withdrawals()
    {
        return $this->morphMany(WithdrawalRequest::class, 'requester');
    }

    public function withdrawalsQuery()
    {
        $withdrawals = $this->withdrawals->pluck('id')->toArray();
        return WithdrawalRequest::whereIn('id', $withdrawals);
    }

    public function transactions()
    {
        return $this->morphMany(WalletTransaction::class, 'owner');
    }

    public function transactionQuery()
    {
        $ids = $this->transactions->pluck('id');
        return WalletTransaction::whereIn('id',$ids);
    }



    public function banks()
    {
        return $this->morphMany(BankDetail::class, 'owner');
    }

    public function getBankAttribute()
    {
        return $this->banks()->latest()->first();
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function keyOfficers()
    {
        return $this->hasMany(KeyOfficer::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function recievedRepayments()
    {
        return $this->repayments->where('reversed', 0);
    }

    public function repaymentsQuery()
    {
        $repayments = $this->repayments->pluck('id')->toArray();
        return Repayment::whereIn('id', $repayments);
    }

    public function getAvatarAttribute($avatar)
    {
        return asset(Storage::url($avatar));
    }

    public function canPlaceInvestment($amount)
    {
        return $this->wallet >= $amount;
    }

    public function verificationRequests()
    {
        return $this->hasMany(InvestorVerificationRequest::class, 'investor_id');
    }

    public function hasPendingVerification()
    {
        if ($this->verificationRequests()->where('status', 2)->first()) {
            return true;
        }
        return false;
    }

    public function getLicenceAttribute($licence)
    {
        return asset(Storage::url($licence));
    }

    public function getRegCertAttribute($cert)
    {
        return asset(Storage::url($cert));
    }

    
    /**
     * Determines that an investor is making his first wallet fund
     * This is only true if the investor has only one wallet fund and 
     * it happened today
     * @return void
     */
    public function isFirstFunding()
    {
        $fundings = $this->transactions->where('code', '000')->count();

        if ($fundings == 1) {
            return true;
        }

        return false;
    }

    public function getBackedLoans()
    {
        $loans = $this->loanFunds()->with('loanRequest')->get()->map(function ($fund) {
            return optional($fund->loanRequest)->loan;
        });

        return $loans->count();
    }

    public function getSuccessfulWithdrawals()
    {
        return $this->withdrawals->where('status', 2);
    }

    public function getCertificateNumberAttribute()
    {
        preg_match('/\d{9}$/', $this->reference, $matches);
        return 'NP-IC-' . $matches[0];
    }

    public function refund($fund)
    {
        $financeHandler = new FinanceHandler(new TransactionLogger);
        $code = config('unicredit.flow')['loan_fund_recovery'];
        $financeHandler->handleSingle(
            $this,
            'credit',
            $fund->amount,
            $fund,
            'W',
            $code
        );
        // fund has been fulfilled
        $fund->update(['status'=>'6']);
    }

    public function restorefund($fund)
    {
        $financeHandler = new FinanceHandler(new TransactionLogger);
        $code = config('unicredit.flow')['loan_fund_recovery'];
        $financeHandler->handleSingle(
            $this,
            'debit',
            $fund->amount,
            $fund,
            'W',
            $code
        );
        // fund has been active
        $fund->update(['status'=>2]);
    }


    public function validateWithdrawals(array $request)
    {
        if($this->banks->count() < 1){

            throw new \InvalidArgumentException(" Withdrawer has no bank account set");
        }

        if(!is_array($request)){
            throw new \InvalidArgumentException("validateWithrawals expected an array ".get_class($request)." is given");
        }

        $walletLimit = Settings::investorMinimumWalletBalance();

        $leftOver = $this->wallet - $request['amount'];

        if(!$request['amount']){
            throw new  \BadFunctionCallException(" Withdrawal amount is not set");
        }
        
        if($this->wallet < $request['amount']){
            throw new \DomainException("Insufficient Funds");
        }
        
        if($leftOver < $walletLimit){
            throw new \DomainException("Your wallet balance cannot be less than limit $walletLimit");
        }

        return true;

    }
}
