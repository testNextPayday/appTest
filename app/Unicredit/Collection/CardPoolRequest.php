<?php
namespace App\Unicredit\Collection;

use App\Models\User;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use App\Models\BillingCard;
use App\Card\ChargeResponse;
use GuzzleHttp\Psr7\Request;
use App\Models\RepaymentPlan;
use GuzzleHttp\Psr7\Response;
use App\Card\CardResponseCollection;
use App\Unicredit\Logs\DatabaseLogger;
use GuzzleHttp\Exception\RequestException;


class CardPoolRequest
{

    protected $zeroStatusBuffers;
    protected $httpRequestBody;

    public $responses;

    public function __construct(

        Client $client,
        BillingCard $card,
        User $user,
        RepaymentPlan $plan
    )
    {

       
        $this->card = $card;
        $this->user = $user;
        $this->plan = $plan;
        $this->headers = [
            'Authorization' => 'Bearer ' .config('paystack.secretKey'),
            'Accept' => 'application/json',
            'Content-Type'=>'application/json'
        ];
        $this->authorizationChargeUrl = config('paystack.authorizationChargeUrl');
        $this->client = $client;
    
        $this->responses = new CardResponseCollection([]);


        // setups
        $this->setZeroStatusBuffers();
        $this->setHttpRequestBody();

        // make the pool request
        $this->makePoolRequest();
       
        return $this->responses;
    }

    
    /**
     * Initialiases the whole sweeping process
     *
     * @return \App\Card\CardResponseCollection
     */
    protected  function makePoolRequest()
    {
  
        $total  = $this->zeroStatusBuffers->count();
        
        if ($total > 0) {

            $requests = function ($total) {
              
                for ($i = 0;$i < $total;$i++){

                    $request = new Request(
                        'POST',
                        $this->authorizationChargeUrl,
                        $this->headers,
                        json_encode($this->httpRequestBody)
                    );
                 
                    yield $request;
                }
            };
           
            $pool = $this->createGuzzlePool($requests);
        
            // Initiate the transfers and create a promise
            $promise = $pool->promise();

            // Force the pool of requests to complete.
            $promise->wait();
        }
       
        return $this->responses;
    }

    
    /**
     * Creates a guzzle pool request
     *
     * @param  mixed $requests
     * @return void
     */
    protected function createGuzzlePool($requests)
    {
     
        return new Pool($this->client, $requests($this->zeroStatusBuffers->count()), [

            'concurrency'=>3,

            'fulfilled'=> function (Response $response, $index){
              
                $this->successCallback($response, $index);
            },

            'rejected'=>function (RequestException $reason, $index){
             
                $this->failureCallback($reason);
            }
        ]);

    }

    protected function setZeroStatusBuffers()
    {
        $buffers = $this->plan->getBuffers();
        
        $this->zeroStatusBuffers = $buffers->where('status', 0);
    }


    protected function setHttpRequestBody()
    {
        $employment = $this->user->employments()->with('employer')->get()->last();
        
        if($employment->employer->vat_fee > 0){
            $vatFee = $employment->employer->vat_fee;            
            $this->httpRequestBody = [
                'email' => $this->user->email,
                'amount' => strval((int)(round($this->plan->emi + $vatFee)/3) * 100), // change to Kobo
                'authorization_code' => $this->card->authorization_code,
            ];
        } 
        
        else{
            $this->httpRequestBody = [
                'email' => $this->user->email,
                'amount' => strval((int)(round($this->plan->emi)/3) * 100), // change to Kobo
                'authorization_code' => $this->card->authorization_code,
            ];
        }       
    }

        
    /**
     * The success response when sweep succeed
     *
     * @param  mixed $response
     * @param  mixed $index
     * @return void
     */
    protected function successCallback($response, $index)
    {
       
        $data = json_decode($response->getBody()->getContents(), true);
        
        $chargeResponse  = new ChargeResponse($data);
     

            // if chrage was successful you update the buffer
        if ($chargeResponse->isSuccessful()) {
        
            // get the first unpaid buffer and update
            $buffer = $this->zeroStatusBuffers->first();

            $buffer->update(
                [
                'date_paid'=>$chargeResponse->getDate(),
                'transaction_ref'=>$chargeResponse->getReference(),
                'dump'=> $response->getBody(),
                'status_message'=> $chargeResponse->getMessage()
                ]
            );
            

            //unset it from the zero status and refresh the plan
            $this->zeroStatusBuffers = $this->zeroStatusBuffers->reject(

                function ($value, $index) use ($buffer){

                    return $value->id === $buffer->id;
                }
            );
        
        }
        
        $this->responses->push($chargeResponse);
    }

    
    /**
     * The response when sweep fails
     *
     * @param  mixed $reason
     * @return void
     */
    protected function failureCallback($reason)
    {
        $msg = json_decode((string)$reason->getResponse()->getBody())->message;

        session()->flash('error', $msg);
        
        return (new DatabaseLogger())->log(
            $this->plan, 
            [
            'title'=>' Failed Card Pool Attempt',
            'message'=> substr($msg, 0, 190),
            'description'=>' The Card Pool for one of the buffers failed',
            'status'=>0
            ]
        );
    }



}
?>