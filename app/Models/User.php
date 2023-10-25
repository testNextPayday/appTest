<?php

namespace App\Models;

use Storage;
use App\Models\Loan;
use App\Models\Refund;
use App\Traits\Payable;

use App\Models\Settings;

use App\Models\RepaymentPlan;
use App\Models\IncompleteRequest;
use Laravel\Passport\HasApiTokens;
use App\Models\BankStatementRequest;
use App\Unicredit\Collection\Utilities;
use App\Traits\ReferenceNumberGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, ReferenceNumberGenerator, HasApiTokens, Payable, HasFactory;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    protected $casts = [
        'settings' => 'array'
    ];

    public function virtualAccount() {
        return $this->hasOne(VirtualAccount::class);
    }
    
    protected $refPrefix = 'NPU-';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public static function boot()
    {
        parent::boot();
        self::creating(function($model) {
            $model->reference = $model->generateReference();
        });
    }
    
    public function referrer()
    {
        return $this->morphTo('adder');
    }

    public function tickets()
    {
        return $this->morphMany(\App\Models\Ticket::class, 'owner');
    }
    
    public function loanRequests()
    {
        return $this->hasMany(LoanRequest::class);
    }
    
    public function bankDetails()
    {
        return $this->morphMany(BankDetail::class, 'owner');
    }
    
    /**
     * This is same with above. This methods was written so there will
     * be a common interface for models partaking in withdrawals
     *
     * @return void
     */
    public function banks() {
        
        return $this->morphMany(BankDetail::class, 'owner');
    }
    
    public function getRouteKeyName()
    {
        return 'reference';
    }
    
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
    
    public function receivedLoans()
    {
        return $this->hasMany(Loan::class)->latest();
    }

    public function updateLoans()
    {
        $id = $this->id;
        $loan = Loan::where('user_id',$id)->where('status', '1')->get()->last();
        return $loan;
    }

    public function activeLoans()
    {
        return $this->receivedLoans->where('status', '1');
    }

    public function activeLoanRequest()
    {
        return $this->loanRequests->whereIn('status', [0, 1, 2, 4]);
    }
    
    public function employers()
    {
        return $this->hasMany(Employer::class);
    }
    
    public function employments()
    {
        return $this->hasMany(Employment::class);
    }
    
    public function withdrawals()
    {
        return $this->morphMany(WithdrawalRequest::class, 'requester');
    }

    public function loanWalletTransactions()
    {
        return $this->hasMany(LoanWalletTransaction::class);
    }
    
    public function transactions()
    {
        return $this->morphMany(WalletTransaction::class, 'owner');
    }
    
    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function okraLogs()
    {
        return $this->hasMany(OkraLog::class);
    }

    public function okraSetup()
    {
        return $this->hasMany(OkraSetup::class);
    }

    public function incompleteRequests()
    {
        return $this->hasMany(IncompleteRequest::class);
    }

    public function repaymentPlans()
    {
        return $this->hasManyThrough(RepaymentPlan::class,Loan::class);
    }


    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
    
    public function billingCards()
    {
        return $this->hasMany(BillingCard::class);    
    }

    public function getGenderDescAttribute()
    {
        return $this->gender == 1 ? 'Male' : 'Female';
    }

    public function lastBankStatementRequest()
    {
        return $this->statementRequest->last();
    }

    public function lastSuccessBankStatementRequest()
    {
        return $this->statementRequest->filter(function($statement) { return $statement->request_doc != null;})->last();
    }

    public function statementRequest()
    {
        return $this->hasMany(BankStatementRequest::class);
    }
    
    /**
     * Adds the last bank as an attribute
     */
    public function getBankAttribute()
    {
        return $this->bankDetails()->latest()->first();
    }
    
    public function getAvatarAttribute($avatar)
    {
        if(str_contains($avatar, 'http')) { 
            return $avatar;
        }
        return asset(Storage::url($avatar));
    }
    
    public function getPassportAttribute($value)
    {
        return asset(Storage::url($value));
    }
    
    public function getGovtIdCardAttribute($value)
    {
        return asset(Storage::url($value));
    }
    
    public function getFirstNameAttribute()
    {
        $names = explode($this->name, ' ');
        return @$names[0] ?? $this->name;
    }
    
    /**
     * This displays how much the borrower is owing at the current point in time
     *
     * @return float
     */
    public function getMaskedLoanWalletAttribute()
    {
        $real_wallet = $this->loan_wallet;
        $due_emis = 0;
        foreach($this->activeLoans() as $loan) {
            // Get due unpaid emis for all loans
            foreach($loan->unpaidDueRepayments() as $duePayments){
                $due_emis += $duePayments->total_amount;
            }
        }
        return $real_wallet - $due_emis;

    }
    
    public function getLastNameAttribute()
    {
        $names = explode($this->name, ' ');
        return @$names[count($names) - 1] ?? $this->name;
    }
    
    public function canPlaceLoanRequest()
    {
        $response['status'] = true;
        
        if(config('unicredit.modules.phone_verification') && !$this->phone_verified) {
            $response['status'] = false;
            $response['message'] = 'Please verify your phone number to proceed';
            return $response;
        }
        
        if(!$this->personalProfileIsComplete()) {
            $response['status'] = false;
            $response['message'] = 'Please complete your personal profile to proceed';
            return $response;
        }
        
        if(!$this->familyProfileIsComplete()) {
            $response['status'] = false;
            $response['message'] = 'Please complete your family profile to proceed';
            return $response;
        }

       

        // if (! $this->bvnVerified()) {
        //     $response['status'] = false;
        //     $response['message'] = ' Please verify your bvn to continue';
        //     return $response;
        // }
         
        // $applicationFee = Settings::loanRequestFee();
        // if($applicationFee > $this->wallet) {
        //     $response['status'] = false;
        //     $response['message'] = "You need at least ₦{$applicationFee} (Loan application charges) to continue";
        // }
        
        return $response;
    }


    public function canAccessLoanRequestPage() 
    {
        if (Settings::loanProcessingDisabled()) {

            $response['status'] = false;
            $response['message'] = 'Loan Request cannot be made at this point in time';
            return $response;
        }

        if ($this->activeLoans()->count() > 0 )  {

            $response['status'] = false;
            $response['message'] = 'You have an active loan running. You should either settle or topup';
            return $response;
        }


    }

    

    public function bvnVerified() 
    {
        return $this->bvn_verified;
    }
    
    public function generateRemitaAuthCode()
    {
        $code = $this->generateRemitaCode();
        return $this->update(['remita_auth_code' => $code]);
    }
    
    public function basicProfileIsComplete()
    {
        if(!$this->name || !$this->phone /*|| !$this->getOriginal('avatar')*/) return false;
           
        return true;
    }
    
    public function personalProfileIsComplete()
    {
        if(!$this->gender ||  !$this->dob || !$this->address || !$this->city || 
            !$this->state || !$this->lga || !$this->occupation /*||
                !$this->getOriginal('passport') || !$this->getOriginal('govt_id_card')*/
                ) 
                
                return false;
            
        return true;
    }

    
    /**
     * If user has a non primary employer in employment
     *
     * @return bool
     */
    public function employerIsNotPrimary()
    {
        foreach($this->employments as $employment) {
            if(optional($employment->employer)->is_primary != 0) {
                return false;
            }
        }
        return true;
    }
    
    public function familyProfileIsComplete()
    {
        if(!$this->marital_status || !is_int($this->no_of_children) || !$this->next_of_kin ||
        !$this->next_of_kin_phone || !$this->next_of_kin_address ||
        !$this->relationship_with_next_of_kin)
            return false;
        return true;
    }
    
    public function profileIsComplete()
    {
        return $this->basicProfileIsComplete() &&
                $this->personalProfileIsComplete() && 
                //$this->familyProfileIsComplete() && 
                ($this->employments()->where('is_current', true)->count() > 0) &&
                ($this->bankDetails->count() > 0);
    }


    public function hasName($name)
    {
        // name matches by spaces
        list($fname, $lname) = explode(' ', $this->name);
        $has_name = (strpos(strtolower($name), strtolower($fname)) !== false) || (strpos(strtolower($name), strtolower($lname)) !== false);

        // if no matches check by comma
        if (!$has_name) {
            list($fname, $lname) = explode(',', $this->name);
            $has_name = (strpos(strtolower($name), strtolower($fname)) !== false) || (strpos(strtolower($name), strtolower($lname)) !== false);
        }
        return $has_name;
    }
}
