<?php
namespace App\Services;

use App\Models\Loan;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;


class DissolveLoanService
{
    
    /**
     * Begins the dissolution of a loan
     *
     * @param  \App\Models\Loan $loan
     * @return void
     */
    public function dissolveLoan(Loan $loan)
    {
        // Cancel Loan Request
        $loanRequest = $loan->loanRequest;
        $loanRequest->update(['status'=> 3]);

        // Returns investor funds and mark as cancelled
        $this->cancelFunds($loanRequest);

        // Delete loan
        $loan->delete();
    }

    protected function cancelFunds($loanRequest)
    {
        $financeHandler = new FinanceHandler(new TransactionLogger);
        $code = config('unicredit.flow')['loan_fund_rvsl'];
        foreach($loanRequest->funds as $fund) {

            if ($fund->status == 2) { // Fund is active
                $investor = $fund->investor;
                // Restore fund to investor
                $financeHandler->handleSingle($investor,  'credit', $fund->amount, $fund, 'W', $code);

                $fund->update(['status'=>'3']);
            }
        }
    }
}