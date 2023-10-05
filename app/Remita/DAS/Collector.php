<?php

namespace App\Remita\DAS;

use App\Models\Loan;


class Collector extends DASService
{
    
    public function stopCollection(Loan $loan)
    {
        // $fields = json_encode(array(
        //     "customerId" => "11417210",
        //     "authorisationCode" => '11112222333434',
        //     "mandateReference" => "300007676417",
        // ));
        
        $loanRequest = $loan->loanRequest;
        $user = $loan->user;
        $fields = json_encode(array(
            "customerId" => $user->remita_id,
            "authorisationCode" => $user->remita_auth_code,
            "mandateReference" => $loanRequest->mandateId,
        ));
    
        $url =  "{$this->baseUrl}stop/loan";
        
        return $this->makePostRequest($url, $this->getPostHeaders(), $fields);
    }
    
    
}