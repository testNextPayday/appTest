<?php
namespace App\Services\WalletTransaction;

use App\Helpers\FinanceHandler;
use App\Models\WalletTransaction;



class CancelTransactionService
{

    protected $financeHandler;

    /** whitelisted transactions we can cancel */
    protected $whitelist = [
        "016", // corrective reversals
        "024" // Loan voids recovery
    ];
    
    /**
     * Setup Cancelling Service
     *
     * @param  mixed $financeHandler
     * @return void
     */
    public function __construct(FinanceHandler $financeHandler)
    {
        $this->financeHandler = $financeHandler;

        
    }

    
    /**
     * Cancel a particular transaction
     *
     * @param  \App\Models\WalletTransaction $transaction
     * @return void
     */
    public function cancelTransaction(WalletTransaction $transaction)
    {
        
        if (! in_array($transaction->code, $this->whitelist)) {

            throw new \DomainException('This transaction cannot be cancelled');
        }

        // update the transaction
        $transaction->update(['cancelled'=> 1]);
        
        // if direction is 1 (incoming) meaning we debit
        $type = $transaction->direction == 1 ? 'debit' : 'credit';
        $owner = $transaction->owner;
        $amount = $transaction->amount;
        $entity = $transaction;
        $code = config('unicredit.flow')['corrective_rvsl'];
        $flow = $transaction->purse == 1 ? 'W' : 'V' ;
       
        $this->financeHandler->handleSingle(
            $owner,
            $type,
            $amount,
            $entity,
            $flow,
            $code
        );
    }
}