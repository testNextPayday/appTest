<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;
use App\Paystack\PaystackService;
use App\Unicredit\Contracts\Visitor;
use App\Services\LoanRepaymentService;
use GuzzleHttp\Exception\ClientException;

class  TransactionVerificationService extends PaystackService 
{
    protected $repaymentService;

    /**
     * Relative path for transaction verification
     * @var string
     */
    protected const VERIFY_URL = '/transaction/verify/';


    public const FAILURE_STRING = 'The card holder was not authorised. This is used in 3-D Secure Authentication';

    /**
     * Implements a visit function to add extended verification 
     * functionality to this class
     *
     * @param  mixed $buffer
     * @return void
     */
    public function verifyBuffer(PaymentBuffer $buffer):void
    {
        try {
            
            $url = self::VERIFY_URL.$buffer->transaction_ref;

            $data = [];
        
            $this->setHttpResponse($url, 'GET', $data);

            $response = $this->retrieveResponse();

            $updates = [
                'status'=> 0,
                'verified'=> 1,
                'dump'=> $this->response->getBody(),
                'status_message'=> $response['data']['gateway_response'],

                // We set amount here back to the plans amount excluding the paystack charge
                //'amount'=> round($buffer->plan->total_amount / 3, 2)
            ];

            if ($this->verificationSuccessful()) {
                $updates['status'] = 1;
            }

            $buffer->update($updates);
            $buffer->refresh();

            if ($buffer->status == 1) {
               
                if (!$this->repaymentService) {
                    $this->setRepaymentService(new LoanRepaymentService());
                }

                $this->repaymentService->makeSuccessfulSplitCollection($buffer);
            }

        }catch (\Exception $e) {

            // catch the exception so nothing happens
            if ($e instanceof ClientException) {

                $err = json_decode((string)$e->getResponse()->getBody());
                $msg = $err->message;
                print ($msg);
                
            } else {
                print($e->getMessage());
            }
            
        }
        
    
    }

    
    /**
     * injects the repayment service to transaction verifier
     *
     * @param  mixed $service
     * @return void
     */
    public function setRepaymentService(LoanRepaymentService $service):void
    {
        $this->repaymentService  = $service;
    }

    public function verifyTransaction($reference):array
    {
        $url = self::VERIFY_URL.$reference;

        $data = [];
    
        $this->setHttpResponse($url, 'GET', $data);

        return $this->retrieveResponse();
        
    }

    
    /**
     * 3D Failure Verification
     *
     * @return bool
     */
    public function verification3DFailure()
    {
        $response  = $this->retrieveResponse();
        
        $failureString = self::FAILURE_STRING;
        $gatewayMsg = $response['data']['gateway_response'];

        $status = $response['data']['status'];

        return $status == 'failed' && $gatewayMsg == $failureString;
    }

    
    /**
     * Verifies that the response was 
     *
     * @return bool
     */
    public function verificationSuccessful():bool
    {
        $response  = $this->retrieveResponse();

        return $response['status'] === true && 
        $response['data']['status'] === 'success';
    }


    
    /**
     * Checks that all buffers has been paid
     * and updates the plan
     * 
     * @param  \App\Models\RepaymentPlan $plan
     * @return void
     */
    public function verifyRepaymentPlans(RepaymentPlan $plan):void
    {
        $penalty = $plan->emi_penalties + ($plan->emi_penalties * (0.1/3));

        if ($plan->allBuffersPaid()) {
            
            $updates = [
                'status'=> true, 
                'collection_mode'=> 'PAYSTACK',
                'status_message' => 'Success',
                'date_paid' => Carbon::now()->toDateTimeString(),
                'paid_amount'=>$plan->emi,
                'should_cancel'=> $plan->issue_ordered ? true : false,
                'last_try_status'=> true,
               
            ];

        } else {

            $updates = [
                'status'=> false,
                'last_try_status'=> false,
                'collection_mode'=> null,
                'paid_amount'=> null,
                'date_paid'=> null,
                'card_tries'=> $plan->card_tries++,
                'last_try'=>Carbon::now()->toDateTimeString(),
                'emi_penalties' => $penalty
            ];            
        }

        $plan->update($updates);
    }

    
    /**
     * Verifies a transaction by marking as paid based on entries from buffers in the wallet
     *
     * @param  mixed $plan
     * @return void
     */
    public function verifyRepaymentPlanOnBuffers(RepaymentPlan $plan)
    {
        if (! $this->repaymentService) {
            $this->setRepaymentService(new LoanRepaymentService());
        }

        if ($plan->allBuffersPaid() && $plan->status == 0) {
            session(['payment_method'=>'PAYSTACK']);
            return $this->repaymentService->makePaymentFromWallet($plan);
        }

        return true;
        
    }
}
