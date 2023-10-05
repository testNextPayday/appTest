<?php

namespace App\Remita;

use App\Traits\Utilities;
use Carbon\Carbon;

abstract class RemitaService
{

    use Utilities;
    
    protected $headers;
    
    protected $merchantId, $apiKey, $apiToken;
    
    protected $baseUrl;
    
    protected $type = 'das';
    
    
    public function __construct()
    {
        $this->headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        
        $test = config("remita.{$this->type}-mode") === 'sandbox' ? '-test' : '';
        
        $config = config("remita.{$this->type}{$test}");
    
        $this->merchantId = $config['merchantId'];
        $this->apiKey = $config['apiKey'];
        $this->apiToken = $config['apiToken'];
        $this->baseUrl = $config['baseUrl'] . 'data/api/v2/payday/';
    }
    
    protected function getPostHeaders()
    {
        $requestId = time();
        $hash = hash('sha512', $this->apiKey . $requestId . $this->apiToken);
        $authorization = "remitaConsumerKey={$this->apiKey}, remitaConsumerToken=${hash}";
        
        return array_merge($this->headers, [
           'MERCHANT_ID: ' . $this->merchantId,
           'API_KEY: ' . $this->apiKey,
           'REQUEST_ID: ' . $requestId,
           'REQUEST_TS: ' . Carbon::now()->format(\DateTime::ISO8601),
           'AUTHORIZATION: ' . $authorization
        ]);
    }
    
}