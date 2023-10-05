<?php

namespace Tests\Unit\System;

use Mockery;
use Tests\TestCase;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;

use App\Models\BillingCard;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Collection\CardService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\utilities\HttpTestResponseFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardServiceTest extends TestCase
{
    use RefreshDatabase;


    
    /**
     * The class for making Http Request
     * 
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     *  Provides fake response
     *  @var \Tests\utilities\HttpTestResponseFactory
     */
    protected $httpResponseFactory;

    /**
     *  The service we want to test
     *  @var \App\Services\TransactionVerificationService
     */
    protected $verifyService;

    /**
     * The card service for sweeping 
     * @var \App\Unicredit\Collection\CardService
     */
    protected $cardService;

    
    /**
     * setup needed bootstrap for testing
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->httpResponseFactory = new HttpTestResponseFactory(
            $type = 'paystack-charge-test'
        );

        $this->mock = new MockHandler([]);

        $this->requestHandler = HandlerStack::create($this->mock);

        $this->httpClient = new Client(['handler'=> $this->requestHandler]);

        $this->cardService  = new CardService($this->httpClient, new DatabaseLogger);

    }


      /**
     * Mocks a success response from server
     *
     * @return void
     */
    protected function getSuccessResponse()
    {
        $successResponse = json_encode(
            $this->httpResponseFactory->createResponse('success')
        );

        return  new Response(200, 
            ['Content-Type'=> 'application/json'], $body = $successResponse
        );
    }


     /**
     * Mocks a failed response from server
     *
     * @return void
     */
    protected function getFailureResponse()
    {
        $failureResponse = json_encode(
            $this->httpResponseFactory->createResponse('failed')
        );

        return  new Response(400, 
            ['Content-Type'=> 'application/json'], $body = $failureResponse
        );
    }

     /**
     * Mocks a failed exception from gateway
     *
     * @return void
     */
    protected function getExceptionResponse()
    {
        return new RequestException('Error Communicating with Server', new Request('GET', 'test'));
    }

    /**
     * Test that the cards service will exit without billing
     *
     * @group Maintenance
     * @author Keania
     * 
     */

    public function testCardServiceExitsWithoutBillingCard()
    {
        
         // STEP 1 : setup all details
         $allSuccessResponse = [
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getExceptionResponse()
        ];

        foreach ($allSuccessResponse as $response) {

            $this->mock->append($response);
        }

        $plan  = factory(RepaymentPlan::class)->create();
        $response  = $this->cardService->attemptInBits($plan);
        $this->assertFalse($response['status']);
        $this->assertTrue($response['message'] === 'User has no card listed');
        $this->assertTrue($response['code'] === 001);
    }


    /**
     * Test that the card service creates payment buffers
     *
     * @author Keania
     * @group Maintenance
     * 
     */
    public function testCardServiceCreatesBuffers()
    {
       
         // STEP 1 : setup all details
         $allSuccessResponse = [
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getExceptionResponse()
        ];

        foreach ($allSuccessResponse as $response) {

            $this->mock->append($response);
        }

        $plan = factory(RepaymentPlan::class)->create();
        $card = factory(BillingCard::class)->create(
            ['user_id'=>$plan->loan->user->id]
        );

        $this->cardService->attemptInbits($plan);

        $plan->refresh();

        $this->assertTrue($plan->buffers->count() == 3);

        // assert that the buffers created contains paystack charge
        $bufferAmount = $plan->totalAmount/3;
        $charge = paystack_charge($bufferAmount);
        $amount = $bufferAmount + $charge;

        $this->assertTrue(round($plan->buffers->first()->amount) == round($amount));
       
    }

    


    /**
     *  Test When some responses fail 
     *
     * @author Keania
     * @group Maintenance
     * 
     */
    public function testCardServiceCreatesBuffersFailsWithOne()
    {
       
         // STEP 1 : setup all details
         $allSuccessResponse = [
            $res = $this->getFailureResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getExceptionResponse()
        ];

        foreach ($allSuccessResponse as $response) {

            $this->mock->append($response);
        }
        $plan = factory(RepaymentPlan::class)->create();
        $card = factory(BillingCard::class)->create(['user_id'=>$plan->loan->user->id]);
        $response = $this->cardService->attemptInbits($plan);
       
        $this->assertFalse($plan->allBuffersPaid());
       
    }


   


    
}
