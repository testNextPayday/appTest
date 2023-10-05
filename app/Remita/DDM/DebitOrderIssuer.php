<?php

namespace App\Remita\DDM;


use App\Models\Loan;

use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;

use App\Remita\RemitaResponse;
use App\Services\LoanRepaymentService;
use App\Remita\RemitaResponseCollection;
use Illuminate\Support\Facades\Log as sysLog;

/**
 * Handles debit order issuing for DDM loans
 * 
 */

class DebitOrderIssuer extends DDMService
{
    
    /**
     * Issues a debit instruction
     * 
     * @param RepaymentPlan $plan
     * @return RemitaResponse
     */
    public function issue(RepaymentPlan $plan)
    {
       
        $loan = $plan->loan;

        
        $userBank = $loan->user->bankDetails()->latest()->first();
        
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/send";
        
        $requestId = time();
        
        // for new loans with is_new set on repayment_plans management fee is now contained in the emi
        $totalAmount = $plan->totalAmount;
        $emi_amount = $totalAmount / 3.0;
        $hashParams = $this->merchantId.$this->serviceTypeId.$requestId.round($emi_amount, 2).$this->apiKey;
        $hash = hash('sha512', $hashParams);
        
        $fields = json_encode(array(
            "merchantId" => $this->merchantId, 
            "serviceTypeId" => $this->serviceTypeId, 
            "hash" => $hash,
            "requestId" => $requestId, 
            "totalAmount" => round($emi_amount, 2),
            "mandateId" => $loan->mandateId,
            "fundingAccount" => $userBank->account_number,
            "fundingBankCode" => $userBank->bank_code,
        ));
        
        $result = $this->makePostRequest($url, $this->headers, $fields);
        
        return new RemitaResponse($result, 'debit-order');
    }



    /**
     * issueInBits
     *
     * @param  RepaymentPlan $plan
     *
     * @return RemitaResponseCollection
     */
    public function issueInBits($plan)
    {
        $loan = $plan->loan;
        $userBank = $loan->user->bankDetails()->latest()->first();
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/send";
        $responses = [];
       
        for($i = 0;$i < 3;$i++){
           
            $requestId = time();
        
            // for new loans with is_new set on repayment_plans management fee is now contained in the emi
            $totalAmount = $plan->totalAmount;
            $emi_amount = $totalAmount / 3.0;
            $hashParams = $this->merchantId.$this->serviceTypeId.$requestId.round($emi_amount, 2).$this->apiKey;
            $hash = hash('sha512', $hashParams);
            
            $fields = json_encode(array(
                "merchantId" => $this->merchantId, 
                "serviceTypeId" => $this->serviceTypeId, 
                "hash" => $hash,
                "requestId" => $requestId, 
                "totalAmount" => round($emi_amount, 2),
                "mandateId" => $loan->mandateId,
                "fundingAccount" => $userBank->account_number,
                "fundingBankCode" => $userBank->bank_code,
            ));
            
            $result = $this->makePostRequest($url, $this->headers, $fields);
           
            $response = new RemitaResponse($result, 'debit-order');
            
            $this->createPaymentBuffer($plan, $response);

            $responses[] = $response;
        }

        return new RemitaResponseCollection($responses);
        
     
    }


    /**
     * Creates a payment buffer for response
     *
     * @param  mixed $plan
     * @param  mixed $response
     * @return void
     */
    protected function createPaymentBuffer(RepaymentPlan $plan, RemitaResponse $response):void
    {
        $amount = round($plan->total_amount / 3, 2);
        $mandateId = @$plan->loan->mandateId;

        PaymentBuffer::create([
            'plan_id'=>$plan->id,
            'amount'=>$amount,
            'requestId'=>$response->getRequestId(),
            'mandateId'=>$mandateId,
            'rrr'=>$response->getRRR(),
            'transaction_ref'=>$response->getTransactionRef(),
            'status_message'=>$response->getMessage(),
            'dump'=> json_encode($response->getRawData())
        ]);
    }

    
    
    
    
    /**
     * Cancels a debit instruction
     * 
     * @param RepaymentPlan $plan
     * @return RemitaResponse
     */
    public function cancel(RepaymentPlan $plan)
    {
        $loan = $plan->loan;
        
        
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/stop";
        
        
        $hashParams = $plan->transaction_ref . $this->merchantId . $plan->requestId . $this->apiKey;
        $hash = hash('sha512', $hashParams);
        
        
        $fields = json_encode(array(
            'merchantId' => $this->merchantId,  
            'hash' => $hash,
            'requestId' => $plan->requestId, 
            'mandateId' => $loan->mandateId,
            'transactionRef' => $plan->transaction_ref
        ));
        
        $result = $this->makePostRequest($url, $this->headers, $fields);
        
        return new RemitaResponse($result, 'debit-cancel');
    }
    

      /**
     * Cancels a debit instruction
     * 
     * @param RepaymentPlan $plan
     * @return RemitaResponseCollection
     */
    public function cancelInBits(RepaymentPlan $plan)
    {
        $loan = $plan->loan;
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/stop";
        $responses = [];

        foreach($plan->buffers->where('status', 0) as $buffer){

            if($buffer->cancelled) continue;
            $hashParams = $buffer->transaction_ref . $this->merchantId . $buffer->requestId . $this->apiKey;
            $hash = hash('sha512', $hashParams);

            $fields = json_encode(array(
                'merchantId' => $this->merchantId,  
                'hash' => $hash,
                'requestId' => $buffer->requestId, 
                'mandateId' => $loan->mandateId,
                'transactionRef' => $buffer->transaction_ref
            ));
            
            $result = $this->makePostRequest($url, $this->headers, $fields);
        
            $response  = new RemitaResponse($result, 'debit-cancel');

            
            if($response->isASuccess()){
                $buffer->update(['cancelled'=>true]);
            }

            $responses[] = $response;
        }
        return new RemitaResponseCollection($responses);
    
    }
    

    
    /**
     * Checks the status of a debit instruction
     * 
     * @param RepaymentPlan $plan
     * @return RemitaResponseCollection
     */
    public function statusInBits(RepaymentPlan $plan)
    {
        $loan = $plan->loan;
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/status";
        
        $responses = [];

        $repaymentService = new LoanRepaymentService();

        //loop through the buffers 
        foreach($plan->buffers as $buffer)
        {
            if($buffer->status == 1) continue;

            $hashParams = $buffer->mandateId . $this->merchantId . $buffer->requestId . $this->apiKey;
            $hash = hash('sha512', $hashParams);

            $fields = json_encode(array(
                'merchantId' => $this->merchantId,  
                'hash' => $hash,
                'requestId' => $buffer->requestId, 
                'mandateId' => $buffer->mandateId,
            ));
            
            $result = $this->makePostRequest($url, $this->headers, $fields);
            $response = new RemitaResponse($result,'debit-status');
           
            $responses[] = $response;
            $statuscode = $response->getStatusCode();

            $buffer->update([
                'status_message'=>$response->getMessage()."($statuscode)",
                'date_paid'=>$response->getlastStatusUpdateTime(),
                'status'=> $statuscode === '00' ? 1 : 0
            ]);

            // make a successful split collection entry
            if ($statuscode == '00') {
                $repaymentService->makeSuccessfulSplitCollection($buffer);
            }
        }
        
        
        return new RemitaResponseCollection($responses);
    }


     /**
     * Checks the status of a debit instruction
     * 
     * @param RepaymentPlan $plan
     * @return Remita
     */
    public function status(RepaymentPlan $plan)
    {
        $loan = $plan->loan;
        
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/status";
        
        
        $hashParams = $loan->mandateId . $this->merchantId . $plan->requestId . $this->apiKey;
        $hash = hash('sha512', $hashParams);
        
        
        $fields = json_encode(array(
            'merchantId' => $this->merchantId,  
            'hash' => $hash,
            'requestId' => $plan->requestId, 
            'mandateId' => $loan->mandateId,
        ));
        
        $result = $this->makePostRequest($url, $this->headers, $fields);
        
        return new RemitaResponse($result, 'debit-status');
    }
}
