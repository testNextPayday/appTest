<?php

namespace App\Models;

use App\Models\LoanNote;
use App\Traits\Conversations;
use App\Traits\ReadableModel;
use App\Models\InvestmentCertificate;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Services\CacheManager\CacheConstants;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable, Conversations, ReadableModel;
    
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'is_active', 'is_verified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];
    
    public function activeLoans()
    {
        $mins = 3 * 60;
        return Cache::remember('admin_active_loans', $mins, function(){
            return Loan::whereStatus("1")->where('is_managed',false)->get();
        });
        
    }

    public function inActiveLoans()
    {
        return Cache::remember('admin_inactive', 3 * 60, function(){
            return Loan::inActive()->get();
        });
    }

    public function amountDisbursed()
    {
        //Note might also included managed loans
        $mins = 3 * 60;
        return Cache::remember('amount_disbursed', $mins, function(){
            return Loan::whereStatus("1")->get()->sum('disbursal_amount');
        });
    }

    public function feesEarned()
    {
        
    }
    public function activeLoansBalance()
    {
        return Cache::get(CacheConstants::A_LN_BALANCES);
    }

    public function inActiveLoansBalance()
    {
        return Cache::get(CacheConstants::IN_A_LN_BALANCES);
    }


    public function fulfilledLoanBalance()
    {
      return Cache::get(CacheConstants::F_LN_BALANCES);
    }

    public function transferLoansBalance()
    {
        return Cache::get(CacheConstants::T_LN_BALANCES);
    }

    public function managedLoansBalance()
    {
        return Cache::get(CacheConstants::MG_LN_BALANCES);
    }

    public function voidLoansBalance()
    {

        return Cache::get(CacheConstants::V_LN_BALANCES);
        
    }


    public function investorsPortFolioSize()
    {
        return Cache::get(CacheConstants::PORT_FOLIO_SIZE);
    }

    public function investorsWalletBalances()
    {
        return Cache::get(CacheConstants::IN_WALLET_BALANCES);
    }

    public function borrowersWalletBalances()
    {
        return Cache::get(CacheConstants::USER_WALLET_BALANCES);
    }

    public function borrowersEscrowBalances()
    {
        return Cache::get(CacheConstants::ESCROW_WALLET_BALANCES);
    }



    public function promissoryInvestorsMaturityValue()
    {
        $mins = 3 * 60;
        return Cache::remember('total_pnotes_maturity_value', $mins, function(){
            return InvestmentCertificate::active()->get()->each(function($item,$index){
           
                $maturity_value = $item->amount + ((($item->amount * ($item->rate/100)) / 12) * $item->tenure );
                $interest = $maturity_value - $this->amount;
                $tax = ($item->tax/100) * ((($item->amount * ($item->rate/100)) / 12) * $item->tenure );
                $item->maturity_value =  $maturity_value - $tax;
            })->sum('maturity_value');
        });
    }

    public function managedLoans()
    {
        return Cache::remember('admin_managed_loans', 3 * 60, function(){
            return Loan::whereStatus("1")->where('is_managed',true)->get();
        });
       
    }

    
    public function voidLoans()
    {
        return Cache::remember('admin_void_loans', 3 * 60, function(){
            return Loan::onlyTrashed()->get();
        });
       
    }

    public function fulfilledLoans()
    {
        return Cache::remember('admin_fulfilled_loans', 3 * 60, function(){
            return Loan::whereStatus("2")->where('is_managed',false)->get();
        });
        
    }
    
    public function activeLoanRequests()
    {
        return LoanRequest::whereStatus(2)->get();
    }
    
    public function pendingLoanRequests()
    {
        $status = [0,1];
        return LoanRequest::whereIn('status', $status)->get();
    }
    
    public function loansOnTransfer()
    {
        return LoanFund::whereStatus("4")->get();
    }
    
    public function borrowers()
    {
        return Cache::remember('admin_all_borrowers', 3 * 60, function(){
            return User::all();
        });
    }
    
    public function investors()
    {
        return Cache::remember('admin_all_investors', 3 * 60, function(){
            return Investor::all();
        });
    }
    
    public function individualInvestors()
    {
        return Cache::remember('individual_investors', 3 * 60, function(){
            return Investor::where('is_company', false)->get();
        });
        
    }
    
    public function corporateInvestors()
    {
        return Cache::remember('company_investors', 3 * 60, function(){
            return Investor::where('is_company', true)->get();
        });
    }
    
    public function activeUsers()
    {
        return Cache::remember('active_users', 3 * 60, function(){
            return User::where('is_active', true)->get();
        });
        
    }
    
    public function activeStaff()
    {
        return Cache::remember('active_staffs', 3 * 60, function(){
            return Staff::where('is_active', true)->get();
        });
        
    }
    
    public function verifiedEmployers()
    {
        return Cache::remember('verified_employers', 3 * 60, function(){
            return Employer::where('is_verified', 3)->get();
        });
        
    }
    
    
    /**
     * Returns user messages
     */
    public function messages()
    {
        return $this->morphMany(Message::class, 'sender');
    }


    /**
     * Admin loan notes
     *
     * @return void
     */
    public function notes()
    {
        return $this->morphMany(LoanNote::class, 'owner');
    }
    
}