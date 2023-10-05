<?php

namespace App\Models;

use Storage;
use App\Models\Loan;
use App\Models\User;

use App\Models\Target;
use App\Models\Meeting;

use App\Models\Message;
use App\Models\Investor;
use App\Models\LoanNote;
use App\Models\Settings;
use App\Models\BankDetail;
use App\Models\LoanRequest;
use App\Traits\Conversations;
use App\Traits\ReadableModel;
use App\Services\TargetService;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\Affiliates\ResetPasswordNotification;

class Affiliate extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, Conversations, ReadableModel;
    
    /**
     * @var array
     */
    protected $guarded = [];
    
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
    
    public function getRouteKeyName()
    {
        return 'reference';
    }

    
    /**
     * Retrieves active affiliates
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    
    /**
     * All targets of this affiliate
     */
    public function targets()
    {
        return $this->morphToMany(Target::class, 'targettable')->withPivot('met');
    }


    public function mappedEmployer()
    {
        return $this->belongsToMany(Employer::class, 'affiliate_employers');
    }
    
    /**
     * Returns borrowers that have used this affiliates ref code
     */
    public function borrowers()
    {
        return $this->morphMany(User::class, 'adder');
    }

    
    /**
     * Returns investors that have used this affiliates ref code
     */
    public function investors()
    {
        return $this->morphMany(Investor::class, 'adder');
    }

    
    /**
     * Affiliates loan notes
     *
     * @return void
     */
    public function notes()
    {
        return $this->morphMany(LoanNote::class, 'owner');
    }
    
    /**
     * Returns withrawal requests
     */
    public function withdrawalRequests()
    {
        return $this->morphMany(WithdrawalRequest::class, 'requester');
    }
    
     /**
     * Returns withrawal requests
     * Ileft the above to avoid breaking any thing and did this one fot the withdrawalhandler class
     */
    public function withdrawals()
    {
        return $this->morphMany(WithdrawalRequest::class, 'requester');
    }
    

    /**
     * Returns wallet transactions
     * 
     */
    public function transactions()
    {
        return $this->morphMany(WalletTransaction::class, 'owner');
    }


    public function earnedCommissions()
    {
        return $this->transactions->filter(function($item,$index){

            return $item->code == '019' || $item->code == '020';
        });
    }
    
    /**
     * Returns loans booked by affiliate
     * 
     */
    public function loans()
    {
        return $this->morphMany(Loan::class, 'collector');
    }
    
    /**
     * Returns an affiliate's supervisor
     * 
     */
    public function supervisor()
    {
        return $this->morphTo('supervisor');
    }
    
    /**
     * Returns related banks
     */
    public function banks()
    {
        return $this->morphMany(BankDetail::class, 'owner');
    }
    
    
    /**
     * Returns user messages
     */
    public function messages()
    {
        return $this->morphMany(Message::class, 'sender');
    }
    
    
    /**
     * Returns user placed loan requests
     */
    public function loanRequests()
    {
        return $this->morphMany(LoanRequest::class, 'placer');
    }
    
    
    /**
     * Returns a user's scheduled meeting
     */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
    
    /**
     * Adds the last bank as an attribute
     */
    public function getBankAttribute()
    {
        return $this->banks()->latest()->first();
    }
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    
    public function getAvatarAttribute($avatar)
    {
        return asset(Storage::url($avatar));
    }
    
    public function getReferralLinkAttribute()
    {
        return route('register') . '/?rc=' . strtolower($this->reference);
    }
    
    /**
     * Returns a public url for an affiliate's CV
     */
    public function getCvAttribute($cv)
    {
        return asset(Storage::url($cv));
    }


    public function validateWithdrawals(array $request)
    {
        if( $this->banks->count() < 1){
            throw new \InvalidArgumentException(" Withdrawer has no bank account set");
        }

        if(!is_array($request)){
            throw new \InvalidArgumentException("validateWithrawals expected an array ".get_class($request)." is given");
        }

        $withdrawLimit = Settings::affiliateMinimumWithdrawal();

        if(!$request['amount']){

            throw new  \BadFunctionCallException(" Withdrawal amount is not set");
        }
        
        if($this->wallet < $request['amount']){

            throw new \DomainException("Insufficient Funds");
        }
        
        if($request['amount'] < $withdrawLimit){

            throw new \DomainException("You cannot withdraw lower than $withdrawLimit");
        }

        return true;

    }

    
    /**
     * Gets the value of a passed in affiliate settings
     *
     * @param  string $arg
     * @return string
     */
    public function settings($arg)
    {
        if (!$this->settings) {
            return null;
        }
        return json_decode($this->settings)->{$arg} ?? null;
    }


    
}
