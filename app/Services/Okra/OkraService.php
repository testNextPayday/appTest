<?php
namespace App\Services\Okra;

use App\Models\Settings;
use GuzzleHttp\Client;

/**
 *  Default abstract class for okra
 */

class OkraService 
{

   protected $baseUrl;
   protected $response;   
   
   /**
    * Initialize the okra service
    *
    * @param  mixed $client
    * @return void
    */
   public function __construct(Client $client)
   {       $this->client = new $client(
           [              
               'headers' => [
                   'Content-Type'  => 'application/json',
                   'baseUrl'=> 'https://api.okra.ng/v2/',                   
                   'Authorization' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2MGMxZjBlMTQzZGY0YTMwMDRkOTZiN2MiLCJpYXQiOjE2MjMzMjYzMzh9.Ehym9v6JpUhZ2Pl1QxnUTXKl6-mkzWBYaXkb2WbbFUI',                  
                   'apiKey'=> '5f4d65c2-9261-56df-9e26-30da82302f59'
               ]
           ]
       );
   }

   

    public function initiatePayment($debitAccnt,$creditAccnt,$amount, $currency)
    {   
        $url = 'https://api.okra.ng/v2/pay/initiate';
        //$url = $this->baseUrl.$relativeUrl;
        $data = ['account_to_debit' => $debitAccnt,
                 'account_to_credit' => $creditAccnt,
                 'amount'=> $amount,
                 'currency'=> 'NGN'
                ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }

    public function verifyPayment($paymentId)
    {   
       
        $url = 'https://api.okra.ng/v2/pay/verify';
        $data = [
                   'payment_id' => $paymentId,                 
                ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    } 
    
    public function checkBalance($balanceId)
    {   
        $url = 'https://api.okra.ng/v2/balance/getById';
        $data = [
                   'id' => $balanceId,                 
                ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }
    
    public function refreshBalance($accountId)
    {   
        $url = 'https://api.okra.ng/v2/balance/refresh';
        $data = [
                   'account_id' => $accountId,                 
                ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }

    public function getBankAccountDetails($accountId)
    {   
        $url = 'https://api.okra.ng/v2/account/getById';
        $data = [
                   'id' => $accountId,                 
                ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }

   
    public function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }
}

?>