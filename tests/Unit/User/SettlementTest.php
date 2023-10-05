<?php

/** The Settlement Test class */

namespace Tests\Unit\User;

use Mockery;

use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Settlement;
use App\Models\RepaymentPlan;
use Unicodeveloper\Paystack\Paystack;
use Illuminate\Support\Facades\Storage;
use Tests\Unit\Traits\TestSettlementSetup;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\utilities\HttpTestResponseFactory;
use App\Unicredit\Managers\SettlementManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PDF;

/**
 * This is the user settlement test class
 */
class SettlementTest extends TestCase
{

    use RefreshDatabase;
    
     /**
     * Setsup mock parameters for testing
     * @return void
     */
    public function setupTest()
    {
       
        $this->mock = Mockery::mock(Paystack::class);

        $settlementManager = new SettlementManager();

        $settlementManager->setPaymentHandler($this->mock);

        $this->app->instance(SettlementManager::class, $settlementManager);

        $this->httpResponseFactory = new HttpTestResponseFactory(
            $type='paystack-charge-test'
        );

        $this->user = factory(\App\Models\User::class)->create();

        //ensure users are verified
        $this->user->update(['email_verified'=>1]);

        $this->loan = factory(\App\Models\Loan::class)->create();

        $this->loan->update(['user_id'=> $this->user->id]);

        $this->loan->loanRequest->update(['user_id'=>$this->user->id]);

        $plans = factory(\App\Models\RepaymentPlan::class, 6)->create(
            ['loan_id'=>$this->loan->id]
        );

        $this->data = [
            'amount'=>$this->loan->settlement_amount * 100,
            'email'=>$this->user->email,
            'reference'=>'754372902eyrurwsg',
            'metadata'=>json_encode(['loan_reference'=>$this->loan->reference])
        ];
    }

    /**
     * A basic unit test for user creating settlement
     * @group badtest
     * @group Maintenance
     * @author Keania
     */
    public function testUserCanCreateSettlement()
    {
        $this->setupTest();

        $route = route(
            'users.pay.settlement', ['reference'=>$this->loan->reference]
        );
   
        $this->mock->shouldReceive('getAuthorizationUrl')->once()->andReturnSelf();

        $this->mock->shouldReceive("redirectNow");

        $response = $this->actingAs($this->user)->post($route, $this->data);

        // Our program is off to the gateway till he returns we wait
        $response->assertOk();

        
    }

    
    /**
     * Test that user trying to make settlement works
     * @group Maintenance
     * @group badtest
     * @author Keania
     *
     * @return void
     */
    public function testUserGatewayCallbackWorks()
    {
        PDF::shouldReceive('loadView')->andReturnSelf()
            ->getMock()
            ->shouldReceive('save')
            ->andReturn(true);

        $this->setupTest();

        $stud = $this->httpResponseFactory->createResponse('success');

        //enduring the return data is same
        $stud['data']['metadata'] = (array)json_decode($this->data['metadata']);

        $this->mock->shouldReceive('getPaymentData')->once()->andReturn($stud);

        $route = route('users.settlement.payment_callback');

        //check that loan had no settlement
        $this->assertTrue(!isset($this->loan->settlement));

        // hit the callback handler for users
        $response = $this->actingAs($this->user)->get($route);

        // a settlement was created for this loan
        $this->loan->refresh();
        $this->assertTrue(isset($this->loan->settlement));


    }

    
    /**
     * Ensures a user does not try to settle an already settled Loan
     * or A loan with pending settlement
     * @group badtest
     * @group Maintenance
     * @author Keania
     */
    public function testUserCannotSettleAlreadySettledLoan()
    {
        $this->setupTest();

        $settlement = factory(\App\Models\Settlement::class)->create(
            ['loan_id'=>$this->loan->id]
        );

        $route = route(
            'users.pay.settlement', ['reference'=>$this->loan->reference]
        );

        $response = $this->actingAs($this->user)->post($route, $this->data);

        $response->assertStatus(302);
    }

    
    /**
     * Use for Mockery Expectations to work
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();
    }
}
