<?php

namespace App\Remita\DAS;

use App\Models\User;

class Customer extends DASService
{
    
    public function getSalaryData(User $user)
    {
        //$authorizationCode = floor(rand() * 1101233);
        // $authorizationCode = 'RAC342321993';
        $user->generateRemitaAuthCode();
        
        $fields = json_encode(array(
            'authorisationCode' => $user->fresh()->remita_auth_code,
            'authorisationChannel' => 'USSD',
            'phoneNumber' => '08056742315'
        ));
        
        
        // $fields = json_encode(array(
        //     'authorisationCode' => $user->fresh()->remita_auth_code,
        //     'authorisationChannel' => 'WEB',
        //     'phoneNumber' => $user->phone
        // ));

        $url =  "{$this->baseUrl}salary/history/ph";
        
        $result = $this->makePostRequest($url, $this->getPostHeaders(), $fields);
        
        return $result;
    }
    
}