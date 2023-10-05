<?php
namespace App\Services;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;
use App\Models\LoanRequestLevel;

use Illuminate\Support\Facades\Notification;
use App\Notifications\Users\LoanDegradeNotification;
use App\Notifications\Users\SalaryPercenntageUpgradeNotification;
use App\Notifications\Users\SalaryPercentageDowngradeNotification;

class LoanRequestUpgradeService
{
    public function checkLoanRequiresUpgrade(Loan $loan ){
            $user_id = $loan->user_id;
            $user = User::where('id', $user_id)->first();
            
            $flawlessCount = 0;
            $plans = $loan->repaymentPlans; 
            
            $employment = $user->employments->last();
            
            
            if($employment->employer->upgrade_enabled == 1){
                foreach($plans as $plan){ 
                    $datePaid = Carbon::parse($plan->date_paid)->format('Y-m-d');
                    if(isset($datePaid)){
                        if ($datePaid <= Carbon::parse($plan->payday)->format('Y-m-d')){
                            $flawlessCount++;
                        } 
                    }else{
                        if (Carbon::parse($plan->updated_at)->format('Y-m-d') <= Carbon::parse($plan->payday)->format('Y-m-d')){
                            $flawlessCount++;
                        }
                    }                                   
                }
                $duration = $loan->duration;
    
                $percentile = ($flawlessCount/$duration) * 100;
    
                if( $percentile >=  66 ){
                    return true;
                } // The loan requires upgrade
                else{
                    return false;
                }
            }else{
                return false;
            }
            
             // Loan does not require upgrade
    }

    public function upgradeUser(Loan $loan)
    {
        $user_id = $loan->user_id;
        $user = User::where('id', $user_id)->first();
        if($user->salary_percentage < 100){
            LoanRequestLevel::create([
                'loan_id' => $loan->id,
                'user_id' => $user_id,
                'salary_percentage' => 20, 
                'cancelled' => 0 //if cancelled is = 0, then the salary_percentage increased
            ]);            
            $salaryPercent = $user->salary_percentage + 20;
            $user->update(['salary_percentage'=> $salaryPercent]);
            
            //notify borrower
            
            $user->notify(new SalaryPercenntageUpgradeNotification($loan));
            
        }
    }

    public function checkLoanRequiresDowngrade(Loan $loan)
    {
        $loanRequestLevel = LoanRequestLevel::where('loan_id', $loan->id)->latest()->first();
        if(isset($loanRequestLevel) && $loanRequestLevel->cancelled){
            return false;
        }else{
            return true;
        }
    }

    public function downgradeUser(Loan $loan){
        $user_id = $loan->user_id;
        $user = User::where('id', $user_id)->first();
        $loanRequestLevel = LoanRequestLevel::where('loan_id', $loan->id)->latest()->first();
        if($user->salary_percentage <= 100){
          if($loanRequestLevel){
                $loanRequestLevel->update([
                    'cancelled' => 1 //if cancelled is = 1, then the salary_percentage decreased by 20
                ]);
                
                $salaryPercent = $user->salary_percentage - 20;
                $user->update(['salary_percentage'=> $salaryPercent]);
            }
           
            //notify borrower
            $user->notify(new SalaryPercentageDowngradeNotification($loan));
            
            
        }
    }
}
