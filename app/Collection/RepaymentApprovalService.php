<?php
namespace App\Collection;

use App\Models\Loan;
use App\Models\Settlement;
use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;
use App\Collection\LoanWalletLogger;
use App\Models\LoanWalletTransaction;
use App\Collection\CollectionFinanceHandler;
use App\Events\LoanWalletTransactionApproved;

class RepaymentApprovalService
{

    protected $collectionHandler;
    
    
    /**
     * __contruct
     *
     * @param  mixed $collectionHandler
     * @return void
     */
    public function __construct()
    {
        $this->collectionHandler = new CollectionFinanceHandler(new LoanWalletLogger);

    }
    
    
    

    /**
     * handleBulkApproval
     *
     * @return void
     */
    public function handleBulkLogApproval($data)
    {
        foreach($data as $upload) {
           
            $trnx = LoanWalletTransaction::find($upload);

            isset($trnx) ? $this->fireApprovalEvent($trnx) : false;
        }
    }

    
    /**
     * I decided this should no longer approve plans but only store the money in wallet
     *
     * @param  mixed $buffer
     * @return void
     */
    public function handleSuccessBufferApproval(PaymentBuffer $buffer)
    {
        $trnx = LoanWalletTransaction::where('buffer_id', $buffer->id)->first();
        $this->collectionHandler->approveTrnx($trnx);
        //return true;
    }

    /**
     * Handle successful plan approval
     *
     * @param  mixed $buffer
     * @return void
     */
    public function handleSuccessPlanApproval(RepaymentPlan $plan)
    {
        $trnx = LoanWalletTransaction::where('plan_id', $plan->id)->first();
        return isset($trnx) ? $this->fireApprovalEvent($trnx) : false;
    }

    /**
     * Handle successful plan approval
     *
     * @param  mixed $buffer
     * @return void
     */
    public function handleSettlementApproval(Settlement $settlement)
    {
        $trnx = LoanWalletTransaction::where('settlement_id', $settlement->id)->first();
        return isset($trnx) ? $this->fireApprovalEvent($trnx, $settlement) : false;
    }

     /**
     * Handle successful topup approval
     *
     * @param  mixed $buffer
     * @return void
     */
    public function handleTopupApproval(Loan $loan)
    {
        $trnx = LoanWalletTransaction::where('loan_id', $loan->id)->where('is_topup', true)->first();
        return isset($trnx) ? $this->fireApprovalEvent($trnx, $loan) : false;
    }

    
    /**
     * Dispatch a transaction for approval
     *
     * @param  mixed $trnx
     * @return void
     */
    protected function fireApprovalEvent($trnx, $obj=null)
    {
        $this->collectionHandler->approveTrnx($trnx);
        // fire an confirmation event on the account
        event(new LoanWalletTransactionApproved($trnx, $obj)); 
    }
    
    
}