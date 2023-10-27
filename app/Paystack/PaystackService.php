<?php
namespace App\Paystack;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;


/**
 * PaystackService Helps implement access to paystack
 */
class PaystackService 

{

    protected $baseUrl;

    protected $secretKey;

    protected $response;

   

    
    /**
     * Sets up configurations needed for paystack 
     *
     * @param  mixed $client
     * @return void
     */
    public function __construct(Client $client)

    {

        $this->setKey();
        $this->setBaseUrl();
        $this->setHttpClient($client);
       
        
    }


     /**
     * Get Base Url from Paystack config file
     */
    protected function setBaseUrl()
    {
        // Check if PAYSTACK_PAYMENT_URL is set in the .env file
        if (env('PAYSTACK_PAYMENT_URL')) {
            $this->baseUrl = env('PAYSTACK_PAYMENT_URL');
        } else {
            // Use the default value from the configuration
            $this->baseUrl = config('paystack.paymentUrl');
        }

    }

    /**
     * Get secret key from Paystack config file
     */
    protected function setKey()
    {
        $this->secretKey = Config::get('paystack.secretKey');
    }

    
    /**
     * Sets the handler for the class to and already 
     * constructed instance of the client
     *
     * @param  mixed $client
     * @return void
     */
    public function setClientInstance(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    
    /**
     * Setups the client by means of dependency inversion
     */
    public function setHttpClient($client)
    {

        $authBearer = 'Bearer '. $this->secretKey;

        
        $this->client = new $client(
            [
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => $authBearer,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json'
                ]
            ]
        );
        
    }

    
    /**
     * Sets the response from paystack
     *
     * @param  string $relativeUrl
     * @param  string $requestMethod
     * @param  array $body
     * @param  boolean $query
     * @return $this
     */
    public function setHttpResponse($relativeUrl, $requestMethod, $body, $query=false)
    {
        if (is_null($requestMethod)) {

            throw new InvalidArgumentException(
                " Cannot send http request with no method specified"
            );
        }
       
        $url = $this->baseUrl.$relativeUrl;

        if ($query) {
            
            $this->response = $this->client->{strtolower($requestMethod)}($url,
                ["query"=>$body]
            );
            
        }else {

            $this->response = $this->client->{strtolower($requestMethod)}(
                $url,
                ["body"=>json_encode($body)]
            );
        }       

        return $this;
    }


        
    /**
     * Get the response from paystack
     *
     * @return void
     */
    protected function retrieveResponse()
    {
        return json_decode($this->response->getBody(), true);
    }
    
    /**
     * Retrieve data from response
     *
     * @return void
     */
    protected function retrieveResponseData()
    {
        return $this->retrieveResponse()['data'];
    }

    public function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }


}