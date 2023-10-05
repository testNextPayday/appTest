<?php
namespace App\Services\CacheManager;

use App\Models\Loan;
use App\Models\User;
use App\Models\Investor;
use App\Models\LoanFund;
use App\Models\PromissoryNote;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheManager\CacheConstants;

class AdminDashboardService
{
    
    /**
     * Updates the active loans cache
     *
     * @return void
     */
    public static function setActiveLoansBalance()
    {
        $activeBalances =  Loan::active()->with('repaymentPlans')->get()->map(function($e){
            return $e->getUnpaidRepayments();
        });
        $sum = array_sum($activeBalances->toArray());
        Cache::forever(CacheConstants::A_LN_BALANCES, $sum);
    }

    /**
     * Updates the active promissory cache
     *
     * @return void
     */
    public static function setActivePaydayNoteSum()
    {
        $sum =  PromissoryNote::active()->get()->sum('principal');
        Cache::forever(CacheConstants::A_PAYDAY_NOTE_SUM, $sum);
    }

    /**
     * Updates the current value promissory cache
     *
     * @return void
     */
    public static function setCurrentPaydayNoteSum()
    {
        $sum =  PromissoryNote::active()->get()->sum('current_value');
        Cache::forever(CacheConstants::A_PAYDAY_NOTE_CURRENT_SUM, $sum);
    }

     /**
     * Updates the in active loans cache
     *
     * @return void
     */
    public static function setInActiveLoansBalance()
    {
        $inActiveBalances =  Loan::inActive()->with('repaymentPlans')->get()->map(function($e){
            return $e->getUnpaidRepayments();
        });
        $sum = array_sum($inActiveBalances->toArray());
        Cache::forever(CacheConstants::IN_A_LN_BALANCES, $sum);
    }

     /**
     * Updates the in fulfilled loans cache
     *
     * @return void
     */
    public static function setFulfilledLoansBalance()
    {
        $fullfilledLoans = Loan::fulfilled()->with('repaymentPlans')->get();
        $sum = 0;
        foreach ($fullfilledLoans as $loan)
        {
            if ($loan->repaymentPlans->count() > 0) {
                foreach($loan->repaymentPlans as $plan) {
                    $sum += $plan->totalAmount;
                }
            }
        } 
       
        Cache::forever(CacheConstants::F_LN_BALANCES, $sum);
    }

     /**
     * Updates the transferred loans cache
     *
     * @return void
     */
    public static function setTransferredLoansBalance()
    {
        $transferLoans = LoanFund::whereStatus("4")->get()->map(function($item, $index){
            return $item->currentValue;
        });
        $sum = array_sum($transferLoans->toArray());;
    
        Cache::forever(CacheConstants::T_LN_BALANCES, $sum);
    }
    
    /**
     * Set manafed loans balances
     *
     * @return void
     */
    public static function setManagedLoansBalance()
    {
        $managedLoans = Loan::managed()->with('repaymentPlans')->get()->map(function($e){
            return $e->getUnpaidRepayments();
        });
        $sum =  array_sum($managedLoans->toArray());

        Cache::forever(CacheConstants::MG_LN_BALANCES, $sum);
    }

    /**
     * Set void loans balances
     *
     * @return void
     */
    public static function setVoidLoansBalance()
    {
        $voidBalances =  Loan::onlyTrashed()->with('repaymentPlans')->get()->map(function($e){
            return $e->getUnpaidRepayments();
        });
        $sum =  array_sum($voidBalances->toArray());

        Cache::forever(CacheConstants::V_LN_BALANCES, $sum);
    }
    
    /**
     * Set All Investors Portfolio
     *
     * @return void
     */
    public static function setAllPortfolioSize()
    {
        $portFolioSize = Investor::with('loanFunds')->get()->map(function($investor){
            return $investor->portfolioSize();
        });

        $sum = array_sum($portFolioSize->toArray());

        Cache::forever(CacheConstants::PORT_FOLIO_SIZE, $sum);
    }
    
    /**
     * Set All Investors Wallet Balanace
     *
     * @return void
     */
    public static  function setInvestorsWalletBalance()
    {
        $sum = Investor::sum('wallet');

        Cache::forever(CacheConstants::IN_WALLET_BALANCES, $sum);
    }
    
    /**
     * Set All Borrower Wallet Balance
     *
     * @return void
     */
    public static function setBorrowersWalletBalance()
    {
        $sum = User::sum('wallet');

        Cache::forever(CacheConstants::USER_WALLET_BALANCES, $sum);
    }
    
    /**
     * set Borrowers Escrow Balance
     *
     * @return void
     */
    public static function setBorrowersEscrowBalance()
    {
        $sum = User::sum('escrow');

        Cache::forever(CacheConstants::ESCROW_WALLET_BALANCES, $sum);
    }

}