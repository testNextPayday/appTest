<?php

namespace App\Remita\DDM;


use Carbon\Carbon;
use App\Models\Loan;
use App\Remita\RemitaResponse;
use App\Remita\DDM\CollectionDates;

class MandateManager extends DDMService
{
    /**
     * Sets up a DDM Mandate
     * 
     * @params Loan $loan;
     * 
     * @return array
     */
    public function setupMandate(Loan $loan, $startDate=null, $endDate=null)
    {
        // TODO: Stop execution if user has no bank
        $requestId = time();
        
        $mandateFields = $this->getMandateSetupFields($loan, $requestId);

        //dd($mandateFields);

        $mandateFields['startDate'] = $startDate ? Carbon::createFromFormat('Y-m-d', $startDate)->format('d/m/Y') : $this->getStartDate();
        $mandateFields['endDate'] = $endDate ? Carbon::createFromFormat('Y-m-d', $endDate)->format('d/m/Y') : $this->getEndDate($loan);

        $fields = json_encode($mandateFields);
        
        $headers = $this->getHeaders();
        
        $url =  "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/setup";
        
        $result = $this->makePostRequest($url, $headers, $fields);
        
        return new RemitaResponse($result, 'mandate-setup');
    }
    
    /**
     * Checks the status of a DDM Mandate
     * 
     *  @param Loan $loan
     * 
     *  @return array
     */
    public function checkMandateStatus(Loan $loan)
    {
        $requestId = $loan->requestId;
        
        $hash = hash('sha512', "{$loan->mandateId}{$this->merchantId}{$requestId}{$this->apiKey}");
        
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/status";
        
        $fields = [
            "merchantId" => $this->merchantId,
        	"mandateId" => $loan->mandateId,
            "hash" => $hash,
            "requestId" => $requestId    
        ];
        
        $result = $this->makePostRequest($url, $this->headers, json_encode($fields));
        
        return new RemitaResponse($result, 'mandate-status');
    }

    
    /**
     * Get History of a mandate
     *
     * @param  mixed $loan
     * @return void
     */
    public function getMandateHistory(Loan $loan)
    {
        $requestId = $loan->requestId;
        
        $hash = hash('sha512', "{$loan->mandateId}{$this->merchantId}{$requestId}{$this->apiKey}");
        
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/history";
        
        $fields = [
            "merchantId" => $this->merchantId,
        	"mandateId" => $loan->mandateId,
            "hash" => $hash,
            "requestId" => $requestId    
        ];
        
        $result = $this->makePostRequest($url, $this->headers, json_encode($fields));
        
        return new RemitaResponse($result);
    }
    
    
    /**
     * Stops a mandate
     * @param Loan $loan
     * @return RemitaResponse
     */
    public function stopMandate(Loan $loan)
    {
        $requestId = $loan->requestId;
        
        $hash = hash('sha512', "{$loan->mandateId}{$this->merchantId}{$requestId}{$this->apiKey}");
        
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/stop";
        
        $fields = [
            "merchantId" => $this->merchantId,
        	"mandateId" => $loan->mandateId,
            "hash" => $hash,
            "requestId" => $requestId    
        ];
        
        $result = $this->makePostRequest($url, $this->headers, json_encode($fields));
        
        return new RemitaResponse($result, 'mandate-stop');
    }
    
    
    /**
     * Returns the download url for a loan's mandate
     * @param Loan $loan
     * @return String 
     */
    public function getMandateUrl(Loan $loan)
    {
        $hash = hash('sha512', $this->merchantId . $this->apiKey . $loan->requestId);
            
        return "{$this->baseUrl}ecomm/mandate/form/{$this->merchantId}/{$hash}/{$loan->mandateId}/{$loan->requestId}/rest.reg";
    }
    
    
    /**
     * Determines collection start date for a loan
     * @return String date
     */
    private function getStartDate()
    {
       return CollectionDates::getStartDate()->format('d/m/Y');;
    }
    
    
    /**
     * Determines collection end date for a loan
     * @param Loan $loan
     * @return String date
     */
    private function getEndDate(Loan $loan)
    {
        return CollectionDates::getEndDate($loan)->format('d/m/Y');;
    }
    
    
    /**
     * Generates hash for remita requests
     */
    private function generateHash($monthlyPayment, $requestId)
    {
        $hashParams = $this->merchantId.$this->serviceTypeId.$requestId.$monthlyPayment.$this->apiKey;
        return hash('sha512', $hashParams);
    }
    
    
    /**
     * Returns mandate setup fields
     * 
     */
    private function getMandateSetupFields(Loan $loan, $requestId)
    {
        // $start_date = $this->getStartDate();
        // $end_date = $this->getEndDate($loan);
        $user = $loan->user; 
        $bank = $user->bankDetails()->latest()->first();
        // so loan amount is now divided by 3
        $amount = round(($loan->emi + $loan->loanRequest->fee($loan->amount))/ 3, 2);


        return [
            'merchantId' => $this->merchantId, 
            'serviceTypeId' => $this->serviceTypeId, 
            'hash' => $this->generateHash($amount, $requestId),
            'payerName' => $user->name,
            'payerEmail' => $user->email,
            'payerPhone' => $user->phone,
            'payerBankCode' => $this->getPayerBankCode($bank->bank_code),
            'payerAccount' => $bank->account_number,
            "requestId" => $requestId, 
            "amount" => $amount,
            // "startDate" => $start_date,
            // "endDate" => $end_date,
            "mandateType" => "DD",
            "maxNoOfDebits" => 3,
            // "maxNoOfDebits" => $loan->duration,
        ];
    }
    
    /**
     * Gets the remita specific bank code for the bank (rare cases though)
     *
     * @param  string $code
     * @return string
     */
    protected function getPayerBankCode($code) 
    {
        $remitaSpecificBanks = config('remita.remita_specific_bank_code');
        if (array_key_exists($code, $remitaSpecificBanks)) {
            return $remitaSpecificBanks[$code][0];
        }
        return $code;
    }
    
    
    /**
     * Formats response
     * 
     */
    private function returnStatusResponse($result)
    {
        if($result && @$result->isActive) {
            $response['status'] = true;
            $response['message'] = 'Mandate is Active';
        }  
        
        $response['status'] = false;
        $response['message'] = 'Mandate has not been activated';
        
        return $response;
    }
    
}
