<?php
/**
 * Class handles card attempts using specified provider
 * 
 */

namespace App\Unicredit\Collection;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use App\Card\ChargeResponse;
use GuzzleHttp\Psr7\Request;
use App\Models\RepaymentPlan;
use GuzzleHttp\Psr7\Response;
use App\Models\{BillingCard, User};
use App\Card\CardResponseCollection;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Contracts\CardSweeper;
use GuzzleHttp\Exception\RequestException;
use App\Unicredit\Collection\CardPoolRequest;
use GuzzleHttp\Exception\{ClientException, ServerException};

class CardService implements CardSweeper{
    
    private $headers;
    
    private $authorizationChargeUrl;
    
    private $client;
    
    public function __construct(Client $client, DatabaseLogger $dbLogger)
    {
        $this->headers = [
            'Authorization' => 'Bearer ' .config('paystack.secretKey'),
            'Content-Type' => 'application/json'
        ];
        
        $this->authorizationChargeUrl = config('paystack.authorizationChargeUrl');
       
        $this->client = $client;
        $this->dbLogger = $dbLogger;

      
    }


    /**
     * Takes a repayment plan an attempts a sweep
     *
     * @param  RepaymentPlan $plan
     *
     * @return array
     */
    public function attemptInBits(RepaymentPlan $plan)
    {
    
        $batch = session()->get('batch');
        if ($batch) {
            // set plan buffers to batch
            $this->attachBatch($plan, $batch);
        }
        
        $user = $plan->loan->user;
        
        $card = $user->billingCards->last();
        
        if (!$card) {
            // if no cards return info
            return [
                'status' => false,
                'message' => 'User has no card listed',
                'code' => 001
            ];
        }
    
        $poolRequest =  new CardPoolRequest(
            $this->client, $card, $user, $plan
        );
        $responses = $poolRequest->responses;

        $this->logSweepData($plan, $responses);

        $this->updatePlanTries($plan);
    
    }

    
    /**
     * Updates  a plans after sweeps
     *
     * @return void
     */
    private function updatePlanTries($plan)
    {
        $update = [
            'card_tries' => $plan->card_tries + 1,
            'last_try' => now()->toDateTimeString()
        ]; 

        $plan->update($update);
                    
    }
    
    /**
     * Logs data from sweeps into the log
     *
     * @param  mixed $plan
     * @param  mixed $response
     * @return void
     */
    private function logSweepData($plan, $responses)
    {
        // Log response
        $logData = [
            'title' => "Card charge attempt for " . $plan->loan->reference,
            'description' => "Card charge attempt for " . $plan->loan->reference,
            'data_dump' => $responses->getDumps() ?? '',
            'message' => $responses->getMessages(),
            'status' => $responses->allSuccessful()
        ];
        
        $this->dbLogger->log($plan, $logData);
    }

    
    /**
     * Used when we do batch sweeping and a bacth value is stored in the session
     *
     * @param  mixed $plan
     * @param  mixed $batch
     * @return void
     */
    private function attachBatch($plan, $batch) 
    {
        
        $buffers = $plan->getBuffers();

        $buffers->each(
            function ($buffer) use ($batch) {

                $buffer->update(['batch'=>$batch]);
            }
        );
    }

    
    
    
    private function chargeCard(BillingCard $card, User $user, $amount)
    {
        $body = [
            'email' => $user->email,
            'amount' => $amount * 100, // change to Kobo
            'authorization_code' => $card->authorization_code,
        ];
        
        try {
            
            $post = $this->client->post($this->authorizationChargeUrl, [
               'headers' => $this->headers,
               'json' => $body
            ]);
            
            $data = json_decode($post->getBody()->getContents(), true);
            //dump($data);
            $response['dump'] = json_encode($data);
            
            if ($data['status'] && strtolower($data['data']['status']) == 'success') {
                $response['status'] = true;
                $response['message'] = @$data['data']['gateway_response'] ?? 'Charge approved';//$data['message'];
                $response['type'] = 'normal';    
                
            } else {
                $response['status'] = false;
                $response['message'] = @$data['data']['gateway_response'] ?? 'Payment not approved';
                $response['type'] = 'normal';   
            }
            
        } catch (ClientException $e) {
            
            $response = $this->buildErrorResponse($e);
            
        } catch (ServerException $e) {
            
            $response = $this->buildErrorResponse($e);
            
        }
        return $response;
    }
    
    private function buildErrorResponse($e)
    {
        $response['status'] = false;
        $response['message'] = $e->getResponse()->getBody()->getContents();
        $response['type'] = 'exception';
        return $response;
    }
    
}