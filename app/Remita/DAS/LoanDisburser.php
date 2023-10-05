<?php

namespace App\Remita\DAS;

use App\Models\Loan;
use Carbon\Carbon;

use App\Remita\RemitaResponse;

class LoanDisburser extends DASService
{
    
    public function disburse(Loan $loan, $monthlyPayment)
    {
        $user = $loan->user;
        
        // $fields = json_encode(array(
        //     "customerId" => "11417210",
        //     "authorisationCode" => $user->remita_auth_code,
        //     "authorisationChannel" => "USSD",
        //     "phoneNumber" => "08126227772",
        //     "accountNumber" => "04410010101",
        //     "currency" => "NGN",
        //     "loanAmount" => "20000",
        //     "collectionAmount" => "21000",
        //     "dateOfDisbursement" => "20-07-2018 11:49:25+0000",
        //     "dateOfCollection" => "21-12-2018 11:49:25+0000",
        //     "totalCollectionAmount" => "210000",
        //     "numberOfRepayments" => "10"
        // ));
        
       // dd($fields);
        
        
        $fields = json_encode(array(
            "customerId" => $user->remita_id,
            "authorisationCode" => $user->remita_auth_code,
            "authorisationChannel" => "WEB",
            "phoneNumber" => $user->phone,
            "accountNumber" => $user->bankDetails()->first()->accountNumber,
            "currency" => "NGN",
            "loanAmount" => $loan->amount,
            "collectionAmount" => $monthlyPayment,
            "dateOfDisbursement" => Carbon::now()->format('d-m-Y H:i:sO'),
            "dateOfCollection" => $this->startDate(),
            "totalCollectionAmount" => $monthlyPayment * $loan->duration,
            "numberOfRepayments" => $loan->duration
        ));
    
        $url =  "{$this->baseUrl}post/loan";
        
        $result = $this->makePostRequest($url, $this->getPostHeaders(), $fields);
    
        return new RemitaResponse($result, 'das-disburse');
    }
    
    private function startDate()
    {
        $now = Carbon::today();
        
        $startDate = Carbon::today();

        // start date is always 25th
        $startDate->day = 25;
        
        // if today is > 18, start month is next month
        if ($now->day > 18) {
            $startDate->addMonth();
        }
        
        return $startDate->format('d-m-Y H:i:sO');
    }
    
}