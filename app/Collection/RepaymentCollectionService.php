<?php
namespace App\Collection;

use App\Models\User;

use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;
use App\Collection\LoanWalletLogger;
use App\Models\LoanWalletTransaction;
use App\Traits\ReferenceNumberGenerator;
use App\Collection\CollectionFinanceHandler;


/***
 * The repayment service is responsible for handling repayment upload
 */
class RepaymentCollectionService {

    use ReferenceNumberGenerator;
    
    protected $code = '036';

    protected $refPrefix = 'UC-LWT-';

    public function __construct()
    {
        $this->financeHandler = new CollectionFinanceHandler(new LoanWalletLogger);
    }


    
    /**
     * Takes a group of upload and logs it
     *
     * @param  mixed $data
     * @return void
     */
    public function handleBulkUploads(array $data)
    {
        foreach($data as $upload) {
            $this->logUpload($upload);
        }
    }

    
    /**
     * Handles successful buffer upload
     *
     * @param  \App\Models\PaymentBuffer $buffer
     * @return void
     */
    public function handleSuccessBufferUpload(PaymentBuffer $buffer)
    {
        $logData = $this->generateBufferUploadData($buffer);
        LoanWalletTransaction::create($logData);
    }

    /**
     * Handles successful plan upload
     *
     * @param  \App\Models\RepaymentPlan $plan
     * @return void
     */
    public function handleSuccessPlanUpload(RepaymentPlan $plan)
    {
        $logData = $this->generatePlanUploadData($plan);
        LoanWalletTransaction::create($logData);
    }

    /**
     * Generate RepaymentPlan Upload Data
     *
     * @param  mixed $buffer
     * @return void
     */
    protected function generatePlanUploadData($plan)
    {
        $loan = $plan->loan;
        $user = $loan->user;

        return [
            'user_id'=> $user->id,
            'loan_id'=> $loan->id,
            'plan_id'=> $plan->id,
            'amount'=> $plan->total_amount,
            'collection_method'=> 'PAYSTACK',
            'collection_date'=> now(),
            'is_logged'=> true,
            'status'=> 1,
            'description'=> 'Paystack payment',
            'code'=> $this->code,
            'reference'=> $this->generateReference(LoanWalletTransaction::class),
            'direction'=> 1
        ];
    }

    /**
     * Generate RepaymentPlan Upload Data
     *
     * @param  mixed $buffer
     * @return void
     */
    protected function generateBufferUploadData($buffer)
    {
        $plan = $buffer->plan;
        $loan = $plan->loan;
        $user = $loan->user;

        return [
            'user_id'=> $user->id,
            'loan_id'=> $loan->id,
            'plan_id'=> $plan->id,
            'amount'=> $buffer->amount,
            'collection_method'=> 'PAYSTACK',
            'collection_date'=> now(),
            'is_logged'=> true,
            'status'=> 1,
            'description'=> 'Paystack payment',
            'code'=> $this->code,
            'reference'=> $this->generateReference(LoanWalletTransaction::class),
            'direction'=> 1,
            'is_buffered'=> true,
            'buffer_id'=> $buffer->id
        ];
    }

    
    /**
     * Handle Settlement Upload
     *
     * @param  mixed $settlement
     * @return void
     */
    public function handleSettlementUpload($settlement)
    {
        $logData = $this->generateSettlementUploadData($settlement);
        LoanWalletTransaction::create($logData);
    }

    /**
     * Handle Topup Upload
     *
     * @param  \App\Models\Loan $settlement
     * @return void
     */
    public function handleTopupUpload($loan)
    {
        $logData = $this->generateTopupUploadData($loan);
        LoanWalletTransaction::create($logData);
    }


    /**
     * Generate Buffer Upload Data
     *
     * @param  mixed $buffer
     * @return void
     */
    protected function generateTopupUploadData($loan)
    {

        $user = $loan->user;

        return [
            'user_id'=> $user->id,
            'loan_id'=> $loan->id,
            'amount'=> $loan->getTopupDeficit(),
            'collection_method'=>'Top up',
            'collection_date'=> now(),
            'is_logged'=> true,
            'status'=> 1,
            'description'=> 'Topup collection',
            'code'=> $this->code,
            'reference'=> $this->generateReference(LoanWalletTransaction::class),
            'direction'=> 1,
            'is_topup'=> true
        ];
    }

    
    /**
     * Generate Buffer Upload Data
     *
     * @param  mixed $buffer
     * @return void
     */
    protected function generateSettlementUploadData($settlement)
    {

        $loan = $settlement->loan;
        $user = $loan->user;

        return [
            'user_id'=> $user->id,
            'loan_id'=> $loan->id,
            'amount'=> $settlement->amount,
            'collection_method'=>'Settlement',
            'collection_date'=> $settlement->paid_at,
            'is_logged'=> true,
            'status'=> 1,
            'description'=> 'Settlement collection',
            'code'=> $this->code,
            'reference'=> $this->generateReference(LoanWalletTransaction::class),
            'direction'=> 1,
            'is_settlement'=> true,
            'settlement_id'=> $settlement->id
        ];
    }

    

    /**
     * Logs an upload
     *
     * @param  mixed $data
     * @return void
     */
    protected function logUpload($data)
    {
        // TODO : Log data over here
        $logData  = $this->generateUploadData($data);
        if (!$logData) return false;
        LoanWalletTransaction::create($logData);
    }


    
    /**
     * Generate Upload Data
     *
     * @param  array $data
     * @return void
     */
    public function generateUploadData($data)
    {
        $user = User::findorFail($data['borrower']);

        return [
            'user_id'=> $user->id,
            'amount'=> $data['paid_amount'],
            'collection_method'=> $data['payment_method'],
            'collection_date'=> $data['collection_date'],
            'is_logged'=> true,
            'status'=> 1,
            'description'=> $data['remarks'] ?? 'No Msg',
            'code'=> $this->code,
            'reference'=> $this->generateReference(LoanWalletTransaction::class),
            'direction'=> 1
        ];
    }
}