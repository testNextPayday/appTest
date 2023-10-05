<?php

namespace App\Unicredit\Collection;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;
use App\Models\Staff;
use App\Models\Bucket;
use App\Models\Employer;
use App\Models\Investor;
use App\Models\Settings;
use App\Models\Affiliate;
use App\Models\RepaymentPlan;

class Utilities {
    
    /**
     * Returns employers with due sweep periods
     * 
     */
    public static function getEmployersForSweep()
    {
        $today = now()->day;
        
        
        $employers = Employer::where('sweep_start_day', '<=', $today)
                            ->where('sweep_end_day', '>=', $today)->get();
        
        return $employers->filter(function ($employer, $key) {
            return $employer->last_sweep == null || $employer->last_sweep->diffInHours(now()) >= 24 / $employer->sweep_frequency;
        });
    }
    
    /**
     * Returns employers with due peak sweep periods
     */
    public static function getEmployersForPeakSweep()
    {
        $today = now()->day;
        
        $employers = Employer::where('peak_start_day', '<=', $today)
            ->where('peak_end_day', '>=', $today)->get();
            
        return $employers->filter(function ($employer, $key) {
            return !$employer->last_sweep || $employer->last_sweep->diffInHours(now()) >= 24 / $employer->peak_frequency;
        });
    }
    
    
    public static function getLoansForSweep()
    {
        $today = now()->day;
        
        $loans = Loan::unfulfilled()
                        // ->where('sweep_start_day', '<=', $today)
                        // ->where('sweep_end_day', '>=', $today)
                        ->get();
        return $loans;
        // return $loans->filter(function ($loan, $key) {
        //     return $loan->last_sweep == null || $loan->last_sweep->diffInHours(now()) >= 24 / $loan->sweep_frequency;
        // });
    }
    
    
    public static function getDDMRemitaLoans()
    {
        $loans = Loan::unfulfilled()
                    ->ddmRemita()
                    ->get();
        
        return $loans;
    }
    
    public static function getLoansForPeakSweep()
    {
        $today = now()->day;
        
        $loans = Loan::unfulfilled()->where('peak_start_day', '<=', $today)
                            ->where('peak_end_day', '>=', $today)->get();
        
        return $loans->filter(function ($loan, $key) {
            return $loan->last_sweep == null || $loan->last_sweep->diffInHours(now()) >= 24 / $loan->peak_frequency;
        });
    }
    
    
    /**
     * Returns buckets with due sweep periods
     * 
     */
    public static function getBucketsForSweep()
    {
        $today = now()->day;
        
        
        $buckets = Bucket::where('sweep_start_day', '<=', $today)
                            ->where('sweep_end_day', '>=', $today)->get();
        
        return $buckets->filter(function ($bucket, $key) {
            return $bucket->last_sweep == null || $bucket->last_sweep->diffInHours(now()) >= 24 / $bucket->sweep_frequency;
        });
    }
    
    
    /**
     * Returns buckets with due sweep peak periods
     * 
     */
    public static function getBucketsForPeakSweep()
    {
        $today = now()->day;
        
        
        $buckets = Bucket::where('peak_start_day', '<=', $today)
                            ->where('peak_end_day', '>=', $today)->get();
        
        return $buckets->filter(function ($bucket, $key) {
            return $bucket->last_peak_sweep == null || $bucket->last_peak_sweep->diffInHours(now()) >= 24 / $bucket->peak_frequency;
        });
    }
    
    
    /**
     * Gets start date for a loan
     * 
     */
    public static function getStartDate()
    {
        $now = Carbon::today();
        
        $startDate = Carbon::today();

        // start date is always 25th
        $startDate->day = 25;
        
        // if today is > 10, start month is next month
        if ($now->day > 10) {
            $startDate->addMonth();
        }
        
        return $startDate->format('d/m/Y');
    }
    
    /**
     * Gets repayments due for invoicing
     * 
     */
    public static function getRepaymentPlansForInvoicing()
    {
        $tomorrow = Carbon::today()->addDay();
        
        // Restrict to card payments
        return [];
        return RepaymentPlan::whereDate('payday', $tomorrow)
                    ->whereNull('invoice')
                    ->get();
    }
    
    

    
    /**
     * Determins the affiliate by looking at the code
     *
     * @param  mixed $code
     * @return void
     */
    public static function resolveAffiliateFromCode($code) 
    {
        $placerIsAffiliateCode = preg_match("/^NPA-\d{9}$/", $code);
        if ($placerIsAffiliateCode) {
            // Placer is affiliate using code
            $placer = Affiliate::whereReference($code)->first();
            return $placer ?? false ;
        }

        $placerIsInvestor = preg_match("/^(UC-IN|NP-IN)-\d{9}$/", $code);
        if ($placerIsInvestor) {
            // Placer is investor using code
            $placer = Investor::whereReference($code)->first();
            return $placer ?? false;
        }

        $placerIsStaff = preg_match("/^(NPS|UCS)-\d{9}$/", $code);
        if ($placerIsStaff) {
            // Placer is staff using reference
            $placer = Staff::whereReference($code)->first();
            return $placer ?? false;
        }

        $placerIsUserPhone = preg_match("/^\+?(\d{11}|\d{13})$/", $code);
        if ($placerIsUserPhone) {
            // Matches phone number using the following formats
            // 08185695490, +2348185695490, 2348185695490
            // Placer is user using phone number
             $placer  = User::wherePhone($code)->first();
             return $placer ?? false;
        }

        $placerIsUserCode = preg_match("/^(NPU|UCU)-\d{9}$/", $code);
        if($placerIsUserCode) {
            //Placer is user using code
            $placer = User::whereReference($code)->first();
            return $placer ?? false;
        }

        return false;
    }

    
    /**
     * Get the currently logged in user
     *
     * @return void
     */
    public  static function currentlyAuthUser()
    {
        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->user();

        } else if (auth()->guard('staff')->check()) {
            return auth()->guard('staff')->user();

        } else if (auth()->guard('affiliate')->check()) {
            return auth()->guard('affiliate')->user();
            
        } else if (auth()->guard('investor')->check()) {
            return auth()->guard('investor')->user();
            
        }else {
            
            throw new \Exception('Authenticated user not allowwed');
        }
    } 

   
}