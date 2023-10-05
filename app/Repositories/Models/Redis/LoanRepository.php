<?php
namespace App\Repositories\Models\Redis;

use App\Models\Loan;
use App\Models\LoanFund;
use Illuminate\Support\Facades\Cache;
use App\Unicredit\Contracts\Models\ILoanRepository;


class LoanRepository implements ILoanRepository
{

    const EXPIRE_MINUTES = 3;
    /**
     * Retrives active loans
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getActiveStatusLoans()
    {
        return Cache::remember("active_loans", self::EXPIRE_MINUTES, function(){
            return Loan::whereStatus('1')->with(['user.employments'])->latest()->get();
        });
    }


    /**
     * Retrives inactive loans
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getInActiveLoans()
    {
        return Cache::remember("in_active_loans", self::EXPIRE_MINUTES, function(){
            return Loan::whereStatus('3')->with(['user.employments'])->latest()->get();
        });
    }
    
    /**
     * Get eligible topup
     *
     * @return void
     */
    public static function getEligibleTopup()
    {
        return Cache::remember("eligible_topup_loans", self::EXPIRE_MINUTES, function(){
            return Loan::where(['status'=>"1",'is_managed'=>false])->get()->reject(function($loan){
                return $loan->canTopUp() == false;
             });
        });
    } 


     /**
     * Retrieves fulfilled loans
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getFulfilledLoans()
    {
        return Cache::remember("fulfilled_loans", self::EXPIRE_MINUTES, function(){
            return Loan::whereStatus('2')->with(['user.employments'])->latest()->get();
        });
    }
    
    /**
     * getAcquiredFunds
     *
     * @return void
     */
    public static function getAcquiredFunds()
    {
        return Cache::remember("acquired_loan_funds", self::EXPIRE_MINUTES, function(){
            return LoanFund::whereNotNull('original_id')->latest()->paginate(20);
        });
    }
}