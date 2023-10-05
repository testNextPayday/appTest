<?php
namespace App\Services;

use App\Models\Loan;
use App\Models\User;
use App\Models\Settlement;
use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;

use App\Models\LoanWalletTransaction;
use App\Collection\RepaymentApprovalService;
use App\Collection\RepaymentDeletionService;
use App\Collection\RepaymentCollectionService;
use App\Collection\RepaymentConfirmationService;
use App\Collection\RepaymentUnconfirmationService;


class LoanRepaymentService
{
    
    /**
     * Collection Service
     *
     * @var \App\Collection\RepaymentCollectionService
     */
    protected $collectionService;


    protected $collectionApprovalService;

    protected $collectionConfirmationService;

    protected $collectionUnconfirmationService;

    protected $collectionDeletionService;


    
    public function __construct()
    {
        $this->collectionService = new RepaymentCollectionService();

        $this->collectionApprovalService = new RepaymentApprovalService();

        $this->collectionConfirmationService = new RepaymentConfirmationService();

        $this->collectionUnconfirmationService = new RepaymentUnconfirmationService();

        $this->collectionDeletionService = new RepaymentDeletionService();
    }

    
    /**
     * makeBulkUpload
     *
     * @param  mixed $data
     * @return void
     */
    public function makeBulkUploads($data)
    {
        return $this->collectionService->handleBulkUploads($data);
    }
    

    /**
     * makeBulkUpload
     *
     * @param  mixed $data
     * @return void
     */
    public function makeBulkApprovals($data)
    {
        return $this->collectionApprovalService->handleBulkLogApproval($data);
    }

    /**
     * makeBulkDeletions
     *
     * @param  mixed $data
     * @return void
     */
    public function makeBulkDeletions($data)
    {
        return $this->collectionDeletionService->handleBulkLogDeletion($data);
    }
    
    /**
     * Confirmation 
     *
     * @param  mixed $user
     * @return void
     */
    public function makeConfirmation(LoanWalletTransaction $trnx)
    {
        return $this->collectionConfirmationService->handlePlanConfirmation($trnx);
    }

    /**
     * Confirmation  Settlement
     *
     * @param  mixed $user
     * @return void
     */
    public function makeSettlementConfirmation($user)
    {
        return $this->collectionConfirmationService->handleSettlementConfirmation($user);
    }

     /**
     * Confirm  Topup
     *
     * @param  mixed $user
     * @return void
     */
    public function makeTopupConfirmation($user)
    {
        return $this->collectionConfirmationService->handleTopupConfirmation($user);
    }

    /**
     * Collect and Approve   Topup
     *
     * @param  mixed $user
     * @return void
     */
    public function makeTopupCollection($loan)
    {
        $this->collectionService->handleTopupUpload($loan);
        return $this->collectionApprovalService->handleTopupApproval($loan);
    }

    
    /**
     * Handles Successful Buffer Split Payments
     *
     * @param  \App\Models\PaymentBuffer $buffer
     * @return void
     */
    public function makeSuccessfulSplitCollection(PaymentBuffer $buffer)
    { 
        $this->collectionService->handleSuccessBufferUpload($buffer);
        $this->collectionApprovalService->handleSuccessBufferApproval($buffer);
    }

    
    /**
     * deleteLoanTransaction
     *
     * @param  mixed $trnx
     * @return void
     */
    public function deleteLoanTransaction(LoanWalletTransaction $trnx)
    {
        // decide direction of movement
        if ($trnx->type() == 'credit') {
            $wallet = $trnx->user->loan_wallet;
            $newBalance = $wallet - $trnx->amount;
            $trnx->user->update(['loan_wallet'=>$newBalance]);
            $trnx->delete();

            return true;
        }

        if($trnx->type() == 'debit') {
            $wallet = $trnx->user->loan_wallet;
            $newBalance = $wallet + $trnx->amount;
            $trnx->user->update(['loan_wallet'=>$newBalance]);
            $trnx->delete();

            return true;
        }

        return false;
    }

     /**
     * Handles Successful Buffer Split Payments
     *
     * @param  \App\Models\RepaymentPlan $plan
     * @return void
     */
    public function makeSuccessfulPlanCollection(RepaymentPlan $plan)
    {
        $this->collectionService->handleSuccessPlanUpload($plan);
        $this->collectionApprovalService->handleSuccessPlanApproval($plan);
    }
    
    /**
     * Make settlement upload
     *
     * @param  \App\Models\Settlement $settlement
     * @return void
     */
    public function makeSettlementUpload(Settlement $settlement)
    {
        $this->collectionService->handleSettlementUpload($settlement);
    }

    /**
     * Make settlement approval
     *
     * @param  \App\Models\Settlement $settlement
     * @return void
     */
    public function makeSettlementApproval(Settlement $settlement)
    {
        $this->collectionApprovalService->handleSettlementApproval($settlement);
    }
    
    /**
     * Make payment from wallet for a particular plan
     *
     * @param  mixed $plan
     * @return void
     */
    public function makePaymentFromWallet(RepaymentPlan $plan)
    {
        $this->checkWalletPayment($plan);
        
        $this->collectionConfirmationService->handleWalletPayment($plan);
    }


    public function checkWalletPayment($plan)
    {
        $user = $plan->loan->user;
        if ($plan->total_amount > $user->loan_wallet) {
            if (abs($plan->total_amount - $user->loan_wallet) <= 50 ) return true;
            throw new \Exception('Wallet cannot pay for this plan');
        }
        return true;
    }

    
    /**
     * Reverses confirmation on a plan
     *
     * @param  mixed $plan
     * @return void
     */
    public function makePaymentUnconfirmation(RepaymentPlan $plan)
    {
        $this->collectionUnconfirmationService->handleUnconfirmPlan($plan);
    }


}