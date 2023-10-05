<?php

namespace Tests\Unit\System;

use Mockery;
use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use App\Models\PaymentBuffer;
use App\Models\RepaymentPlan;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use App\Services\LoanRepaymentService;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Collection\CardService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\utilities\HttpTestResponseFactory;
use App\Services\TransactionVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionVerificationServiceTest extends TestCase
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
     * Parent setup
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

        $this->verifyService = new TransactionVerificationService($this->httpClient);

        $this->verifyService->setClientInstance($this->httpClient);

        $this->verifyService->setRepaymentService(new LoanRepaymentService);

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
     * Test Repayment gets updated if all buffers are cleared
     * @group Maintenance
     * @author Keania
     * 
     * @return void
     */
    public function testRepaymentPlansGetUpdatedIfAllBuffersArePaid()
    {
        // STEP 1 : setup all details
        $allSuccessResponse = [
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getExceptionResponse()
        ];

        // We load all these responses to the mock handler
        foreach ($allSuccessResponse as $response) {

            $this->mock->append($response);
        }

        // STEP 2 : Get a plan
        $plan  = factory(\App\Models\RepaymentPlan::class)->create();

        // Ensure the plan's loan is active
        $plan->loan->update(['status'=> '1']);

        $user = $plan->loan->user;

        $billing = factory(\App\Models\BillingCard::class)->create(['user_id'=> $user->id]);

        // STEP 3 : Charge Plan in Bit
        $response = $this->cardService->attemptInBits($plan);
        
        // STEP 4 : Verify buffers
        $unverifiedBuffers = PaymentBuffer::unverified()->get();

        foreach ($unverifiedBuffers as $buffer) {

            $response = $this->verifyService->verifyBuffer($buffer);
        }

        $plan->refresh();

        $user->refresh();
        $sum = 0;
        // STEP 5 : Buffers are set to true
        foreach ($plan->buffers as $buffer) {

            $sum += $buffer->amount;

            $this->assertEquals($buffer->status, 1);

            // Assert there buffer inflows
            $this->assertDatabaseHas('loan_wallet_transactions', [
                'is_buffered'=> true,
                'buffer_id'=> $buffer->id,
                'amount'=> $buffer->amount
            ]);
        }

        // STEP 6 : Assert wallet balance equals sum
        $this->assertTrue($user->refresh()->loan_wallet == $sum);

        $this->verifyService->verifyRepaymentPlanOnBuffers($plan);
        
        //Assert that the balance is now zero
        //$this->assertTrue($user->refresh()->loan_wallet == 0);

        // STEP 8 : plan has now been verified by repayment service
        $this->assertEquals($plan->refresh()->status, 1);

    }



     /**
     * Test Repayment does not get updated if atleast one fails
     * @group Maintenance
     * @author Keania
     * 
     * @return void
     */
    public function testRepaymentPlansSkipsIfOneFails()
    {
        // STEP 1 : setup all details
        $allSuccessResponse = [
            $res = $this->getFailureResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getFailureResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getSuccessResponse(),
            $res = $this->getExceptionResponse()
        ];

        foreach ($allSuccessResponse as $response) {

            $this->mock->append($response);
        }

        // STEP 2 : Get a plan
        $plan  = factory(\App\Models\RepaymentPlan::class)->create();

        $user = $plan->loan->user;

        $billing = factory(\App\Models\BillingCard::class)->create(['user_id'=> $user->id]);

        // STEP 3 : Charge Plan in Bit
        $response = $this->cardService->attemptInBits($plan);
        
        // STEP 4 : Verify buffers
        $unverifiedBuffers = PaymentBuffer::unverified()->get();

        foreach ($unverifiedBuffers as $buffer) {

            $this->verifyService->verifyBuffer($buffer);
        }

        $plan->refresh();

        // STEP 5 : Buffers are set to true
        foreach ($plan->buffers as $index=>$buffer) {

            if ($index == 0) {

                $this->assertEquals($buffer->status, 0);

            } else {
                $this->assertEquals($buffer->status, 1);
            }
            
        }

        $balance = $user->refresh()->loan_wallet;

        $sum = $plan->buffers->first()->amount * 2;
        // Assert that 2 buffers are stuck in the 
        $this->assertTrue($balance == $sum);

        // STEP 6 : RepaymentPlan has not  been verified
        $this->assertEquals($plan->status, 0);

        // STEP 7 : Run the verify Repayment script
        $this->verifyService->verifyRepaymentPlans($plan);

        $balance2 = $user->refresh()->loan_wallet;
        // Assert that the balance is unchanged
        $this->assertTrue($balance2 == $sum);

        // STEP 8 : plan has not been verified
        $this->assertEquals($plan->refresh()->status, 0);

    }


    
    /**
     * Cleans up mockery container and runs verification tasks needed for expectations
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();
    }
}
