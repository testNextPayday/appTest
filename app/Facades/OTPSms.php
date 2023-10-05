<?php
namespace App\Facades;

use GuzzleHttp\Client;
use App\Facades\BaseFacade;
use App\Repositories\SmsInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class OTPSms extends BaseFacade  implements SmsInterface
{
    use Queueable;

    protected $apiToken;

    protected $client;

    protected $response;

    protected $baseUrl;

    protected $sender;

    protected $senderId;

    public function __construct()
    {
       
        $this->setToken();
        $this->setFrom();
        $this->setBaseUrl();
        $this->setRequestOptions();
        $this->setSenderId();
    }


     /**
     * setToken
     *  Set the user token for the request
     * @return void
     */
    public function setToken()
    {
        $this->apiToken = Config::get('sms.termii.apiToken');

    }

     /**
     * SetFrom
     *  Set the From Parameter
     * @return void
     */
    public function setFrom()
    {
        $this->sender = Config::get('sms.termii.sender');
    }

    /**
     * setFrom
     *  Set the From Parameter
     * @return void
     */
    public function setSenderId()
    {
        $this->senderId = Config::get('sms.termii.senderId');
    }

    /**
     * setBaseUrl
     * 
     *  Set the bulk sms Url
     * @return void
     */
    public function setBaseUrl()
    {
        $apiToken = $this->apiToken;
        $sender = $this->sender;
        $this->baseUrl = Config::get('sms.termii.baseUrl');
        // dd($this->baseUrl);
    }

   
    /**
     * setRequestOptions
     *  Set any other http request option
     * @return void
     */
    public function setRequestOptions()
    {
        $this->client = new Client([
            'base_uri'=>$this->baseUrl
        ]);
    }

     // Wondering why i have to do this
    // Am adding this method to confirm to the SmsInterface
    // while leaving the send method so i statically call it 
    public function sendSMS($to,$msg)
    {
        $this->send($to, $msg);
    }

    protected function send($to,$msg)
    {
      
        
        if (is_null($to) || is_null($msg))
        {
            throw new \Exception('Empty parameters wont work here');
        }
        $url = $this->baseUrl;
       
        $query = array(
            "to" => $to,
            "from" => $this->senderId,
            "sms" => $msg,
            "type" => "plain",
            "channel" => "dnd",
            "api_key" => $this->apiToken
            ); 
            $ch = curl_init($url);       
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);                                              
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
           
            if (curl_error($ch)) {
                return curl_error($ch);

            } else {

                $this->response = $response;
                return $this; 

            }
            curl_close($ch);
    }

  

    public function getResponse()
    {
        return json_decode($this->response->getBody(),true);
    }

   

   
}
?>