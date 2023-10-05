<?php

namespace Tests\Unit\User;

use Mockery;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use App\Services\RefundTransactionService;
use App\Events\PaystackCustomerRefundEvent;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\utilities\HttpTestResponseFactory;
use App\Services\TransactionVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanRequestTest extends TestCase
{

    protected $responseProvider;

    public function setUp(): void
    {
        parent::setUp();

        $this->responseProvider = new HttpTestResponseFactory($type = 'paystack-charge-test');
    }
    
    /**
     * Get mocked
     *
     * @param  mixed $exptdResponse
     * @return void
     */
    protected function getMockSetups($exptdResponse)
    {
        $trnxService = Mockery::mock(TransactionVerificationService::class);

        $this->app->instance(TransactionVerificationService::class, $trnxService);

        $trnxService->shouldReceive('verifyTransaction')->andReturn($exptdResponse);
        $trnxService->shouldReceive('verificationSuccessful')->andReturn(true);

        $refundService = Mockery::mock(RefundTransactionService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        
        $rfndResponse = $this->responseProvider->createResponse('success');
        $refundService->shouldReceive('setHttpResponse')->andReturn(true);
        $refundService->shouldReceive('retrieveResponse')->andReturn($rfndResponse);
        $this->app->instance(RefundTransactionService::class, $refundService);
    }

    
    /**
     * Test we can store loan requests
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testWeCanStoreLoanRequest()
    {
        //Notification::fake();
        $employment = factory(\App\Models\Employment::class)->create();

        $user = $employment->user;

        $data = [
            'bank_statement'=> 'https://www.fakestatement.pdf',
            'comment'=> 'Business only',
            'employment_id'=> $employment->id,
            'charge'=> 300,
            'duration'=> 3,
            'interest_percentage'=> 5,
            'amount'=> 20000,
            'application_fee'=> 617,
            'enableMono'=> true
        ];

        $route = route('users.loan-requests.store');

        $response = $this->actingAs($user)->post($route, $data);
    
        $response->assertStatus(200);
    }


    /**
     *  Test with all conditions met Verification is successful
     *  @group Maintenance
     *  @author Keania
     * @return void
     */
    public function testWeCanSuccessfullyVerifyPayment()
    {
        // Create a user
        $user = factory(\App\Models\User::class)->create();

        // Get a success response from fake factory
        $successTrnx = $this->responseProvider->createResponse('success');

        // Set card expiration to a date in the future
        $successTrnx['data']['authorization']['exp_month'] = now()->addMonths(12)->format('m');
        $successTrnx['data']['authorization']['exp_year'] = now()->addYears(1)->format('Y');

        $ref = $successTrnx['data']['reference'];

        $this->getMockSetups($successTrnx);

        $route = route('users.loan-requests.verifypayment');

        $data = ['reference'=> $ref, 'duration'=> 3];

        Event::fake();

        $response = $this->actingAs($user)->post($route, $data);

        // We assert that a paystack refund event was not dispatched
        Event::assertNotDispatched(PaystackCustomerRefundEvent::class);

        $response->assertStatus(200);

        $response->assertJsonFragment(['status'=>true, 'message'=> 'Verification Successful']);
    }


    /**
     *  Test verification fails when card expires before loan duration
     *  @group Maintenance
     *  @author Keania
     * @return void
     */
    public function testVerificationFailsCardExpiration()
    {
        // Create a user
        $user = factory(\App\Models\User::class)->create();

        // Get a success response from fake factory
        $successTrnx = $this->responseProvider->createResponse('success');

        // Set card expiration to now
        $successTrnx['data']['authorization']['exp_month'] = now()->format('m');
        $successTrnx['data']['authorization']['exp_year'] = now()->format('Y');

        $ref = $successTrnx['data']['reference'];

        $this->getMockSetups($successTrnx);

        Event::fake();
        $route = route('users.loan-requests.verifypayment');

        $data = ['reference'=> $ref, 'duration'=> 3];

        $response = $this->actingAs($user)->post($route, $data);
        Event::assertDispatched(PaystackCustomerRefundEvent::class);
        
        $response->assertStatus(422);
        $response->assertJsonFragment(['status'=>false, 'message'=> 'This card will expire before the loan']);
    }
}
