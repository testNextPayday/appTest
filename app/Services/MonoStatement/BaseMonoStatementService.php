<?php
namespace App\Services\MonoStatement;

use App\Models\Settings;
use GuzzleHttp\Client;
use Carbon\Carbon;

/**
 *  Default abstract class for mono bank statement
 */

class BaseMonoStatementService 
{

   protected $baseUrl;
   protected $response;   
   
   /**
    * Initialize the bank statement service
    *
    * @param  mixed $client
    * @return void
    */
   public function __construct(Client $client)
   {

       $this->baseUrl = $this->getBaseUrl();

       $this->client = new $client(
           [
               'base_uri' => $this->baseUrl ,
               'verify'=> base_path('cacert-2021-10-26.pem'),
               'headers' => [
                   'Content-Type'  => 'application/json',                   
                   'mono-sec-key'=> $this->getClientSecret(),                   
                   'URL'=> 'https://api.withmono.com'
               ]
           ]
       );
   }

   /**
     * Get Client Secret
     *
     * @return void
     */
    protected function getClientSecret()
    {
        return config('monostatement.clientSecret');
    }
    
    /**
     * Get Base Url
     *
     * @return void
     */
    protected function getBaseUrl()
    {
        return config('monostatement.baseUrl');   
    }

    public function authRequest($code)
    {   
        $relativeUrl = '/account/auth';
        $url = $this->baseUrl.$relativeUrl;
        $data = ['code'=> $code];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }

    public function reAuthRequest($id)
    {   
        $relativeUrl = '/accounts/$id/reauthorise';
        $url = $this->baseUrl.$relativeUrl;
        //$data = ['code'=> $code];
        //$this->response = $this->client->post($url, ['form_params'=>$data]); 
    }

    public function statementRequest($id)
    {    
        //$monthDuration = Settings::bankStatmentPeriodMonths();
        $monthDuration = 12;
        $month = 'last'.$monthDuration.'months';
        $relativeUrl = "/accounts/$id/statement?output=pdf&period=$month";
        $url = $this->baseUrl.$relativeUrl;
        $this->response = $this->client->get($url); 
    }

    public function checkBankInformation($id)
    {    
        $relativeUrl = "/accounts/$id";
        $url = $this->baseUrl.$relativeUrl;
        $this->response = $this->client->get($url); 
    }

    public function initiatePayment($amount, $reference)
    {   
        $relativeUrl = '/v1/payments/initiate';
        $url = $this->baseUrl.$relativeUrl;

                
        $startDate = Carbon::today();
        //start date is 26th
        $startDate->day = 26;
        //$startDate->format('DD-MM-YYYY');
        //$date = $startDate->toDateString();
        $date = $startDate->format('d-m-Y');
        
        $data = [
            'amount'=> $amount * 100,
            'type'=> 'recurring-debit',
            'description'=> 'Loan Collection',
            'reference'=> $reference,
            'interval'=>'monthly',
            'start_date'=> $date,
            'redirect_url'=>'https://app.nextpayday.ng/loan-setup/mono/payment/verify',
        ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }

    public function investorFund($amount, $reference)
    {   
        $relativeUrl = '/v1/payments/initiate';
        $url = $this->baseUrl.$relativeUrl;
        $startDate = Carbon::today();
       
        $date = $startDate->format('d-m-Y');
        
        $data = [
            'amount'=> $amount * 100,
            'type'=> 'onetime-debit',
            'description'=> 'Payday Notes Investment',
            'reference'=> $reference,
            'start_date'=> $date,
            'redirect_url'=>'https://app.nextpayday.ng/investors/promissory-notes/verify-monostatus',
        ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }

    public function verifyPaymentStatus($reference)
    {   
        $relativeUrl = '/v1/payments/verify';
        $url = $this->baseUrl.$relativeUrl;
        $data = [
            'reference'=> $reference,
        ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }

    public function cancelMandate($reference)
    {   
        $relativeUrl = '/v1/payments/cancel';
        $url = $this->baseUrl.$relativeUrl;
        $data = [
            'reference'=> $reference,
        ];
        $this->response = $this->client->post($url, ['form_params'=>$data]); 
    }


    public function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }
}

?>