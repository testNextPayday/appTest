<?php

namespace Tests\Unit\Staff;

use Mockery;
use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Staff;
use App\Models\Settlement;
use App\Models\RepaymentPlan;
use Illuminate\Http\UploadedFile;
use Unicodeveloper\Paystack\Paystack;
use Illuminate\Support\Facades\Storage;
use Tests\Unit\Traits\TestSettlementSetup;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\utilities\HttpTestResponseFactory;
use App\Unicredit\Managers\SettlementManager;
use App\Notifications\Users\SettlementCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PDF;
class SettlementTest extends TestCase
{

   
    use RefreshDatabase;



    public function setUp(): void
    {
        parent::setUp();

        $this->initTest();
    }

    public function initTest()
    {

        $this->setupTest();

        
    }

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
     * Test unpermitted staffs cannot view settlement
     * @group Maintenance
     * @author Keania
     *@group badtest
     * @return void
     */
    public function testUnpermittedStaffCannotViewSettlement()
    {
        $staff = factory(\App\Models\Staff::class)->create();

        $response  = $this->actingAs($staff, 'staff')->get(
            route('staff.add.settlements')
        );

        $response->assertStatus(302); // assert a redirection
    }

    /**
     * A basic test for staff to access settlement page
     *@group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffCanAccessSettlementPage()
    {
        $this->staff = factory(\App\Models\Staff::class)->create();

        $this->staff->update(['roles'=>'manage_settlements']);

        $response  = $this->actingAs($this->staff, 'staff')->get(
            route('staff.add.settlements')
        );

        $response->assertStatus(200);

        $response->assertSeeText('Add Settlement');
    }

    
    /**
     * Test that a staff can view all settlements
     * @group Maintenance
     * @author Keania
     *@group badtest
     * @return void
     */
    public function testStaffCanViewAllSettlements()
    {
        $this->staff = factory(\App\Models\Staff::class)->create();

        $this->staff->update(['roles'=>'manage_settlements']);

        $settlements = factory(\App\Models\Settlement::class, 3)->create();

        $response  = $this->actingAs($this->staff, 'staff')->get(
            route('staff.show.settlements')
        );

        $response->assertStatus(200);

        $reference  = $settlements->first()->reference;

        $response->assertSeeText($reference);
    }

    
    /**
     *  Test that staff can view all settlement
     *  @group Maintenance
     *  @author Keania
     *  @group badtest
     * @return void
     */
    public function testStaffCanViewSingleSettlement()
    {
        $this->staff = factory(\App\Models\Staff::class)->create();

        $this->staff->update(['roles'=>'manage_settlements']);

        $settlement = factory(\App\Models\Settlement::class)->create();

        $response  = $this->actingAs($this->staff, 'staff')->get(
            route('staff.settlement.view', ['reference'=>$settlement->reference])
        );

        $response->assertStatus(200);

        $response->assertSeeText('SETTLEMENT DOCUMENTS');
    }


    
    /**
     * A basic unit test for user creating settlement
     * @group badtest
     *  @group Maintenance
     *  @author Keania
     */
    public function testStaffCanCreateSettlement()
    {
        $this->staff = factory(\App\Models\Staff::class)->create();

        $this->staff->update(['roles'=>'manage_settlements']);

        $this->setupTest();
        
        $route = route(
            'staff.pay.settlement', ['reference'=>$this->loan->reference]
        );
   
        $this->mock->shouldReceive('getAuthorizationUrl')->once()->andReturnSelf();

        $this->mock->shouldReceive("redirectNow");

        $response = $this->actingAs($this->staff, 'staff')->post(
            $route, $this->data
        );

        // Our program is off to the gateway till he returns we wait
        $response->assertOk();

        
    }

    
    /**
     * A simple test to ensure return from gateway does not break
     * @group Maintenance
     * @author Keania
     * @group badtest
     * @return void
     */
    public function testStaffGatewayCallbackWorks()
    {
        Notification::fake();

        PDF::shouldReceive('loadView')->andReturnSelf()
            ->getMock()
            ->shouldReceive('save')
            ->andReturn(true);

        $this->staff = factory(\App\Models\Staff::class)->create();

        $this->staff->update(['roles'=>'manage_settlements']);
        
        $this->setupTest();

       
        $stud = $this->httpResponseFactory->createResponse('success');

        //enduring the return data is same
        $stud['data']['metadata'] = (array)json_decode($this->data['metadata']);

        $this->mock->shouldReceive('getPaymentData')->once()->andReturn($stud);

        $route = route('staff.settlement.payment_callback');

        //check that loan had no settlement
        $this->assertTrue(!isset($this->loan->settlement));

        // hit the callback handler for users
        $response = $this->actingAs($this->staff, 'staff')->get($route);

        // a settlement was created for this loan
        $this->loan->refresh();
        $this->assertTrue(isset($this->loan->settlement));
        $user = $this->loan->user;
        Notification::assertSentTo([$user], SettlementCreated::class);

    }

    
    /**
     * Ensures a user does not try to settle an already settled Loan
     * or A loan with pending settlement
     * @group Maintenance
     * @author Keania
     * @group badtest
     * @return void
     */
    public function testStaffCannotSettleAlreadySettledLoan()
    {
        $this->staff = factory(\App\Models\Staff::class)->create();

        $this->staff->update(['roles'=>'manage_settlements']);
        
        $this->setupTest();

        $settlement = factory(\App\Models\Settlement::class)->create(
            ['loan_id'=>$this->loan->id]
        );

        $route = route(
            'staff.pay.settlement', ['reference'=>$this->loan->reference]
        );

        $response = $this->actingAs($this->staff, 'staff')->post($route, $this->data);

        $response->assertStatus(302);
    }

    
    /**
     * Test that a staff can successfully upload
     * @group Maintenance
     * @author Keania
     * @group justnow
     * @return void
     */
    public function testStaffCanUploadSettlement()
    {
        PDF::shouldReceive('loadView')->andReturnSelf()
            ->getMock()
            ->shouldReceive('save')
            ->andReturn(true);

        Storage::fake('public');
        Notification::fake();
        
        $this->staff = factory(\App\Models\Staff::class)->create();

        $this->staff->update(['roles'=>'manage_settlements']);


        $uploadData = [
            'amount'=> 20000,
            'paid_at'=>now()->toDateString(),
            'loan_id'=>$this->loan->id,
            'method'=>'Test',
            'payment_proof'=> $file = UploadedFile::fake()->image('proof.jpg'),
            'reference'=>$this->loan->reference
        ];

        $route = route(
            'staff.upload.settlement'
        );

        $this->assertFalse(isset($this->loan->settlement));

        $response = $this->actingAs($this->staff, 'staff')->post(
            $route, $uploadData
        );
       
        $this->assertDatabaseHas('settlements', ['amount'=> $uploadData['amount'], 'loan_id'=>$uploadData['loan_id']]);

        $this->loan->refresh();

        $this->assertTrue(isset($this->loan->settlement));

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
