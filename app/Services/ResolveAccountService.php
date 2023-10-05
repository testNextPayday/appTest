<?php
namespace App\Services;

use App\Traits\SendsResponses;
use App\Paystack\PaystackService;


class ResolveAccountService extends PaystackService

{

    

    protected const RESOLVE_URL = '/bank/resolve';
    
    /**
     * Resolve a bank account details
     *
     * @return void
     */
    public function resolveAccount(array $data)
    {
        
        $url = self::RESOLVE_URL;

        validateFields($data, 'bank_code', 'account_number');

        
        $this->setHttpResponse($url, 'GET', $data, $query=true);

        return $this->retrieveResponse();

    }
}