<?php
namespace App\Facades;

use GuzzleHttp\Client;
use App\Facades\BaseFacade;
use Illuminate\Support\Facades\Config;
use App\Repositories\WhatsappInterface;

class WhatsappSMS extends BaseFacade implements WhatsappInterface
{

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
     * setFrom
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
        $this->baseUrl = Config::get('sms.termii.multichannelUrl');
       
    }

   
   

    // Wondering why i have to do this
    // Am adding this method to confirm to the SmsInterface
    // while leaving the send method so i statically call it 
    public function whatsappNotify($to,$msg)
    {
        $this->send($to,$msg);
    }

    protected function send($to,$msg)
    {
      
        $data = [
            'api_key'=>$this->apiToken,
            'channel'=>'whatsapp',
            'data'=> $msg,
            'from'=> $this->senderId,
            'to'=>$to
        ];

        $client  =  new Client();
        $response = $client->post($this->baseUrl,[

            'body'=>json_encode($data)
        ]);

        return $response;
    }



    public function getResponse()
    {
        return json_decode($this->response->getBody(),true);
    }

   

   
}
?>