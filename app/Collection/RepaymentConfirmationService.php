<?php
namespace App\Collection;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;
use App\Models\Settlement;
use App\Collection\LoanWalletLogger;
use App\Models\LoanWalletTransaction;
use App\Collection\CollectionFinanceHandler;

class RepaymentConfirmationService
{

    protected $collectionHandler;

   public function __construct()
   {
        $this->collectionHandler = new CollectionFinanceHandler(new LoanWalletLogger);
   }

   /**
     * Handle wallet payment on plan
     *
     * @param  mixed $plan
     * @return void
     */
    public function handleWalletPayment($plan)
    {
        // create a transaction
        $user = $plan->loan->user;
        $this->collectionHandler->moveCashToPlan($plan, $user);
    }


   /**
    * Handles Settlement Confirmation
    *
    * @param  mixed $user
    * @return void
    */
    public function handleSettlementConfirmation(Settlement $settlement)
    {
        $loan = $settlement->loan;
 
        if ($loan) {
             $this->collectionHandler->moveSettlementCash($loan, $settlement);
        }
        
    }

    /**
    * Handles Topup Confirmation
    *
    * @param  mixed $user
    * @return void
    */
   public function handleTopupConfirmation(Loan $loan)
   {
       return $this->collectionHandler->moveTopupCash($loan);
       
   }

   
   /**
    * Handles Plan Confirmation
    *
    * @param  mixed $user
    * @return void
    */
   public function handlePlanConfirmation(LoanWalletTransaction $trnx)
   {
       $loan = $trnx->user->activeLoans()->first();

       if ($loan) {
            return $this->attemptConfirmation($loan, $trnx);
       }
        return false;
   }

    
    /**
     * Attempts confirmation on 
     *
     * @param  mixed $loan
     * @return void
     */
    protected function attemptConfirmation(Loan $loan, $trnx)
    {
        $today = Carbon::today();
       
        $plan = $loan->repaymentPlans->where('status', 0)->where('payday', '<=', $today)->first();
        $user  = $loan->user;
        if(!$plan) {
            return false;
        }
        if ($this->collectionHandler->checkConfirm($plan, $user->loan_wallet) && isset($plan)) {
            return $this->collectionHandler->moveCashToPlan($plan, $user, $trnx);
        }
        session()->flash('info', 'Incomplete payments will get stuck in wallet');
        //throw new \Exception('Wallet cannot confirm this loan '.$loan->user->name);
    }

}