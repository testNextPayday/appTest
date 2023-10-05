<?php
namespace App\Services\Penalty;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\PenaltyEntry;
use App\Models\PenaltySetting;
use App\Collection\LoanWalletLogger;
use App\Collection\CollectionFinanceHandler;

class PenaltyService
{

    protected $collectFinance;

    public function __construct()
    {
        $this->collectFinance = new CollectionFinanceHandler(new LoanWalletLogger);
    }
    
    /**
     * Applies Penalty to loan
     *
     * @param  mixed $loan
     * @param  mixed $amount
     * @return void
     */
    public function debitPenaltyCollection($loan, $amount, $desc)
    {
        $operation = 'debit';
        $this->collectFinance->logPenaltyCollection($loan, $amount, $desc, $operation);
    }
    
    /**
     * Sets a collection date
     *
     * @param  mixed $date
     * @return void
     */
    public function setCollectionDate($date)
    {
        $this->collectFinance->date = $date;
    }
    
    /**
     * Store the collection made on a penalized loan
     *
     * @param  mixed $data
     * @return void
     */
    public function creditPenaltyCollection($loan, $amount, $desc)
    {
        $operation = 'credit';
        $this->collectFinance->logPenaltyCollection($loan, $amount, $desc, $operation);
    }
    
    /**
     * Setup a penalty setting for passed in entity
     *
     * @param  mixed $entity
     * @param  mixed $data
     * @return void
     */
    public function setup($entity, $data)
    {
        validateFields($data, 'type', 'grace_period', 'value');

        $data['entity_type'] = get_class($entity);
        $data['entity_id'] = $entity->id;
        $penalty = PenaltySetting::create($data);

        return $penalty ?? false;
    }



    /**
     * Update the penalty settings
     *
     * @param  mixed $entity
     * @param  mixed $data
     * @return void
     */
    public function update($entity, $data)
    {
        validateFields($data, 'type', 'grace_period', 'value', 'id');

        $penalty = PenaltySetting::find($data['id']);

        $penalty->update($data);

        return $penalty ?? false;
    }


    
    /**
     * Calculate penal ampunt
     *
     * @param  mixed $amount
     * @param  mixed $setting
     * @return void
     */
    public function getPenalAmount($amount, $setting)
    {
        $type = $setting->type;

        $percentage = ($setting->value/100) * $amount;

        $fixed = $setting->value;

        $amount = $type == 'P' ? round($percentage, 2) : $fixed;

        return $amount;
    }

    
    /**
     * Clears outstanding penalty on a loan
     *
     * @param  mixed $loan
     * @return void
     */
    public function dissolvePenalty(Loan $loan) 
    {
        // Creates a counter loan wallet transaction that cancels negative amount on the wallet
        $user = $loan->user;

        $penaltyDate = \Carbon\Carbon::parse($loan->date_penalized);

        // get only those wallet entries that occurred either same time or later than penalized date
        $entries = $loan->getWalletTransactionsForPenalty()->filter(function($entry) use($penaltyDate) { return $penaltyDate->lte($entry->created_at);});
        
        // So we check all the wallet  transactions for the current penalty
        $credits = $entries->where('direction', 1)->sum('amount');
        
        $debits = $entries->where('direction', 2)->sum('amount');

        // We add all the debit transactions 
        // Subtract all the credit transactions
        $amount = $debits - $credits;
        
        $desc = 'Dissolving Penalty for loan';

        $amount < 0 ? $this->debitPenaltyCollection($loan, abs($amount), $desc) : $this->creditPenaltyCollection($loan, $amount, $desc);
        
        // Clear penalized loan attributes
        $loan->update(['date_penalized'=>null,  'is_penalized'=>0, 'total_penable_debts'=>0]);

        foreach($loan->repaymentPlans as $plan) {
            $plan->update(['is_penalized'=>false]);
        }

        // Delete all penalty Entries
        $entries->each(function($entry) { $entry->delete();});
    }

    
    /**
     * Checks we can dissolve penalty on a loan
     *
     * @param  mixed $loan
     * @return void
     */
    protected function isDissolvable($user)
    {
        return $user->loan_wallet >= 0 ? false : true;
    }
    
    /**
     * Checks and pulls a loan out of penalty
     *
     * @param  mixed $loan
     * @return void
     */
    public function checkAndWithdrawPenalty($loan)
    {
        $user = $loan->user;

        if ($user->loan_wallet > 0 ) {

            // Clear penalized loan attributes
            $loan->update(['date_penalized'=>null,  'is_penalized'=>0]);

            return true;
        }
        return false;
    }

    /**
     * Clears penalty from a loan
     *
     * @param  mixed $loan
     * @return void
     */
    public function takeOutPenalty(Loan $loan)
    {
        // zeroised penalty balance
        $loan->update(['penalty_balance'=> 0, 'is_penalized'=>0]);

        $plans = $loan->repaymentPlans->where('is_penalized', 1);

        foreach($plans as $plan) {

            $plan->update(['is_penalized'=>0]);
        }
    }

   

    
}