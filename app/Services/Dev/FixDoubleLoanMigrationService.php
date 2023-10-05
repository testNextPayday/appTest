<?php
namespace App\Services\Dev;

use App\Models\LoanFund;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use Illuminate\Support\Facades\DB;

/** We had a situation sometime on 16 Feb 2021 where loans where migrated twice
* as a result of the page not refreshing. The class was then written to fix that up
*/


/**
 * Objectives
 * ------------
 * Get a loan fund and confirm it has been sold twice
 * Get the last sold loan fund
 * Revert money back to buyer and debit seller wallet
 * Delete loan fund
 */

class FixDoubleLoanMigrationService
{
    protected $financeHandler;


    public function __construct()
    {
        $this->financeHandler = new FinanceHandler(new TransactionLogger);
    }
    
    /**
     * Gets all funds that were sold more than once
     *
     * @return void
     */
    public static function getMultipleSoldFunds()
    {
        $funds = LoanFund::sold()->get();

        $multipleSold = $funds->filter(function($fund){ return $fund->childFund()->get()->count() > 1; });

        return $multipleSold;
    }
    
    /**
     * Get the number of times a loanfund was sold
     *
     * @param  mixed $fund
     * @return void
     */
    public function getFundSoldCount(LoanFund $fund)
    {
        return $fund->childFund()->get()->count();
    }
    
    /**
     * Get the loan fund that was the last sales entry
     *
     * @param  mixed $fund
     * @return void
     */
    public function getLastSoldChild(LoanFund $fund)
    {
        return $fund->childFund()->get()->last();
    }

    
    /**
     * Reverse Double migration on a loan fund
     *
     * @param  mixed $fund
     * @return void
     */
    public function reverseDoubleMigrate(LoanFund $fund)
    {
        try {
            
            DB::beginTransaction();
            // check fund was sold 
            if ($this->getFundSoldCount($fund) <= 1) { return false;}

            // get the last sold fund
            $reverseFund = $this->getLastSoldChild($fund);

            if ($reverseFund) {

                $buyer = $reverseFund->investor;
                $seller = $fund->investor;
                $code  = config('unicredit.flow')['investor_loanfund_migration_rvsl'];
                // credit buyer and debit seller
                $this->financeHandler->handleDouble(
                    $seller, $buyer, $reverseFund->amount, $fund, 'WTW', $code
                );
                
                $reverseFund->delete();
            }

            DB::commit();

        }catch (\Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
        }
        
    }
}