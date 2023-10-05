<?php

namespace App\Remita\DDM;

use App\Models\Loan;
use GuzzleHttp\Client;
use App\Remita\RemitaResponse;

class MandateActivator extends DDMService
{

    /**
     * Sends an OTP to a user's mobile device
     * 
     */
    public function requestAuthorization(Loan $loan)
    {
        
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/requestAuthorization";
        
        //$timestamp = str_replace(':', '', now()->toW3cString());
        $timestamp = $this->str_lreplace(':', '', now()->toW3cString());

        $rid = time();
        
        $hash = hash('sha512', "{$this->apiKey}{$rid}{$this->apiToken}");
        
        $headers = array_merge($this->getHeaders(), [
            "MERCHANT_ID: $this->merchantId",
            "API_KEY: $this->apiKey",
            "REQUEST_ID: $rid",
            "REQUEST_TS: $timestamp",
            "API_DETAILS_HASH: $hash",
        ]);
        
        $fields = json_encode(array(
            'mandateId' => $loan->mandateId, 
            'requestId' => $loan->requestId, 
        ));
        
        $result = $this->makePostRequest($url, $headers, $fields);
        
        return new RemitaResponse($result, 'mandate-authorization');
    }
    
    
    /**
     * Activates a mandate
     */
    public function activateMandate(Loan $loan,$authParams,$transactionRef)
    {

        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/validateAuthorization";
        
        //$timestamp = str_replace(':', '', now()->toW3cString());
        $timestamp = str_lreplace(':', '', now()->toW3cString());
       
        $rid = time();
        
        $hash = hash('sha512', "{$this->apiKey}{$rid}{$this->apiToken}");
        
        $headers = array_merge($this->getHeaders(), [

            "MERCHANT_ID: $this->merchantId",
            "API_KEY: $this->apiKey",
            "REQUEST_ID: $rid",
            "REQUEST_TS: $timestamp",
            "API_DETAILS_HASH: $hash",
        ]);
       
        
        $fields = array(
            "remitaTransRef" => $transactionRef,
            "authParams" => $authParams
        );


       $fields = json_encode($fields);
       
        $result = $this->makePostRequest($url, $headers, $fields);
        
        return new RemitaResponse($result, 'mandate-activation');
    }
    
}
