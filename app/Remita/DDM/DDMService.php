<?php
/**
 * Parent class for all Remita DDM interactions.
 * Performs functions like loading appropriate config files
 * depending on environment (sandbox or live)
 */
 
namespace App\Remita\DDM;

use App\Traits\Utilities;
use Carbon\Carbon;

abstract class DDMService
{
    use Utilities;
    
    protected $merchantId, $apiKey, $apiToken, $serviceTypeId;
    
    protected $headers;
    
    protected $baseUrl;
    
    protected $type = 'ddm';
    
    public function __construct()
    {
        $test = config("remita.{$this->type}-mode") === 'sandbox' ? '-test' : '';
        
        $config = config("remita.{$this->type}{$test}");
        
        $this->merchantId = $config['merchantId'];
        $this->apiKey = $config['apiKey'];
        $this->apiToken = $config['apiToken'];
        $this->serviceTypeId = $config['serviceTypeId'];
        $this->baseUrl = $config['baseUrl'];
        
        $this->headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $this->config = $config;
    }
    
    
    protected function getHeaders()
    {
        return $this->headers;
    }
    
}