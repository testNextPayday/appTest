<?php

namespace App\Remita\DDM;

use App\Models\Loan;

use App\Traits\Utilities;

use App\Remita\RemitaResponse;

class CollectionChecker extends DDMService
{
    /**
     * Checks the history of a mandate
     * 
     * @param Loan $loan
     * @return RemitaResponse
     */
    public function check(Loan $loan)
    {
        $requestId = $loan->requestId;
       
        $hashParams = $loan->mandateId . $this->merchantId . $requestId . $this->apiKey;
        
        $hash = hash('sha512', $hashParams);
        
        $url = "{$this->baseUrl}exapp/api/v1/send/api/echannelsvc/echannel/mandate/payment/history";
        
        $fields = json_encode(array(
            'merchantId' => $this->merchantId,  
            'hash' => $hash,
            'requestId' => $loan->requestId, 
            'mandateId' => $loan->mandateId,
        ));
        
        $result = $this->makePostRequest($url, $this->headers, $fields);
        
        return new RemitaResponse($result, 'collection-check');
    }
}