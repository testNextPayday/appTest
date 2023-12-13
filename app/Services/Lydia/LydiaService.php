<?php
namespace App\Services\Lydia;

use App\Models\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

/**
 *  Default abstract class for okra
 */

class LydiaService 
{

   protected $baseUrl;
   protected $response;
   protected $client;   
   protected $globalHeaders = ['application'=> 'WebApp'];
   protected $cookieJar;
   
   /**
    * Initialize the lydia service
    *
    * @param  mixed $client
    * @return void
    */
   public function __construct(Client $client)
   {       
        $this->baseUrl = $this->getBaseUrl();
        $this->cookieJar = new CookieJar();
        $this->client = new $client(
            [
                'cookies' => true,
                'base_uri' => $this->baseUrl ,
                'headers' => $this->globalHeaders
            ]
        );
        $this->login();
   }

   /**
     * Get Username
     *
     * @return void
     */
    protected function getUsername()
    {
        return config('lydia.username');
    }

    /**
     * Get Password
     *
     * @return void
     */
    protected function getPassword()
    {
        return config('lydia.password');
    }
    
    /**
     * Get Base Url
     *
     * @return void
     */
    protected function getBaseUrl()
    {
        return config('lydia.baseUrl');   
    }


    protected function login() {
        $relativeUrl = '/v7/Collect/authentication';
        $data = [
            'username' => $this->getUsername(),
            'password' => $this->getPassword()
        ];
        $this->response = $this->client->post($relativeUrl, ['json'=>$data, 'cookies' => $this->cookieJar]); 
    }

    public function createMandate($mandateData = []) {
        $resp = json_decode($this->response->getBody(), true);
        $url = '/v7/Collect/create';
        $data = [
            'enterprise_id' => $resp['default_enterprise_id'],
            'amount' => $mandateData['amount'],
            'generate_payments' => true,
            'frequency' => $mandateData['frequency'],
            'description' => 'Loan Request',
            'start_date' => $mandateData['start_date'],
            'duration' => $mandateData['duration'],
            'payer_data' => [
                'bvn' => $mandateData['bvn'],
                'name' => $mandateData['name'],
                'phone_number' => $mandateData['phone_number'],
                'phone_prefix_id' => 2,
                'email' => $mandateData['email']
            ]
        ];

        try {
            $this->response = $this->client->post($url, ['json'=>$data, 'cookies' => $this->cookieJar]);
            return response()->json(['status'=>true, 'message'=> 'Please check your email to complete Lidya setup'], 200);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $body = $response->getBody()->getContents();
                $body = json_decode($body, true);
                if ($body[0] and $body[0]['error']) {
                    dd($body);
                    throw new \Exception('Lidya Error: ' . $body[0]['error']);
                }else{
                    throw new \Exception($e->getMessage());
                }
            }else{
                throw new \Exception('An Error Occurred');
            }
        }
    }
}

?>