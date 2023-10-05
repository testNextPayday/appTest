<?php
namespace App\Repositories\Models\Redis;

use App\Models\Loan;
use Illuminate\Support\Facades\Cache;
use App\Unicredit\Contracts\Models\ILoanRepository;


class AffiliateLoanRepository implements ILoanRepository
{

    const EXPIRE_MINUTES = 3;
    /**
     * Retrives active loans
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getActiveStatusLoans()
    {
        $affiliate = auth('affiliate')->user();
        $key = 'active_loans:'.$affiliate->reference;

        return Cache::remember($key, self::EXPIRE_MINUTES, function() use($affiliate){

            $allVissible = $affiliate->settings('loan_vissibility') == 'view_all_loans';

            $loans =  $allVissible ? 
                    new Loan : 
                    $affiliate->loans();

            return $loans->whereStatus('1')->with('user.employments')->where('is_managed', false)->latest()->get();

        });
    }


  
    
    /**
     * Get eligible topup
     *
     * @return void
     */
    public static function getEligibleTopup()
    {
        $affiliate = auth('affiliate')->user();
        $key = 'eligible_topup_loans:'.$affiliate->reference;

        return Cache::remember($key, self::EXPIRE_MINUTES, function() use($affiliate){

            $allVissible = $affiliate->settings('loan_vissibility') == 'view_all_loans';

            $loans =  $allVissible ? 
                    new Loan : 
                    $affiliate->loans();

            return $loans->whereStatus('1')->where('is_managed', false)->latest()->get()->reject(
                    function ($loan) {
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
        $affiliate = auth('affiliate')->user();
        $key = 'fulfilled_loans:'.$affiliate->reference;

        return Cache::remember($key, self::EXPIRE_MINUTES, function() use($affiliate){

            $allVissible = $affiliate->settings('loan_vissibility') == 'view_all_loans';

            $loans =  $allVissible ? new Loan : $affiliate->loans();

            return $loans->whereStatus("2")->with('user.employments')->get();

        });
    }
}