<?php
namespace App\Facades;

use Illuminate\Bus\Queueable;

use GuzzleHttp\Client;
use App\Facades\BaseFacade;
use App\Repositories\SmsInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BulkSms extends BaseFacade implements SmsInterface
{
    use Queueable, SerializesModels;

    protected $apiToken;

    protected $client;

    protected $response;

    protected $baseUrl;

    protected $sender;

    protected $senderId = 'Nextpayday';

    public function __construct()
    {
       
        $this->setToken();
        $this->setFrom();
        $this->setBaseUrl();
     
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

   
   


    // Wondering why i have to do this
    // Am adding this method to confirm to the SmsInterface
    // while leaving the send method so i statically call it 
    public function sendSMS($to,$msg)
    {
        $this->send($to, $msg);
    }

    protected function send($to,$msg, $senderId = 'Nextpayday')
    {
    
        if (is_null($to) || is_null($msg))
        {
            throw new \Exception('Empty parameters wont work here');
        }

        $this->senderId = $senderId;

        $url = $this->baseUrl;

        $client = new Client();
        
        $channel = $this->senderId == "N-Alert" ? "dnd" : "generic";
       
        $query = array(
            "to" => $to,
            "from" => $this->senderId,
            "sms" => $msg,
            "type" => "plain",
            "channel" => $channel,
            "api_key" => $this->apiToken
        ); 
       
        // $this->response = $client->post(
        //     $url, 
        //     $query
        // );

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

    
        return $this;
    }

  

    public function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }
   
}
?>