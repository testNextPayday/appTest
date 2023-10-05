<?php

namespace Tests\Unit\Admin;

use PDF;
use Mockery;
use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Admin;
use App\Models\Settlement;
use App\Models\RepaymentPlan;
use Illuminate\Http\UploadedFile;
use App\Models\LoanWalletTransaction;
use Unicodeveloper\Paystack\Paystack;
use Illuminate\Support\Facades\Storage;
use Tests\Unit\Traits\TestSettlementSetup;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\utilities\HttpTestResponseFactory;
use App\Unicredit\Managers\SettlementManager;
use App\Notifications\Users\SettlementCreated;
use App\Notifications\Users\SettlementApproved;
use App\Notifications\Users\SettlementDeclined;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
     * 
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
     * Admin can view all pending settlements
     * @group Maintenance
     * @author Keania
     * @group badtest
     * @return void
     */
    public function testAdminCanViewPendingSettlement()
    {
        $this->admin = factory(\App\Models\Admin::class)->create();
        $settlements = factory(\App\Models\Settlement::class, 3)->create();

        $response  = $this->actingAs($this->admin, 'admin')->get(
            route('admin.settlement.pending')
        );

        $response->assertStatus(200);

        $reference  = $settlements->first()->reference;

        $response->assertSeeText($reference);

    }
    
    /**
     * Test admin can view confirmed settlements
     * @group Maintenance
     * @author Keania
     * @group badtest
     * @return void
     */
    public function testAdminCanViewConfirmedSettlement()
    {
        $this->admin = factory(\App\Models\Admin::class)->create();

        $settlements = factory(\App\Models\Settlement::class, 3)->create();

        $settlements->first()->update(['status'=>2]);

        $response  = $this->actingAs($this->admin, 'admin')->get(
            route('admin.settlement.confirmed')
        );

        $response->assertStatus(200);

        $reference  = $settlements->first()->reference;

        $response->assertSeeText($reference);

    }

    /**
     * Test admin can confirm settlement successfully
     *@group Maintenance
     * @author Keania
     * @group badtest
     * @return void
     */
    public function testAdminCanConfirmSettlement()
    {
        PDF::shouldReceive('loadView')->andReturnSelf()
            ->getMock()
            ->shouldReceive('save')
            ->andReturn(true);

        Notification::fake();

        $this->admin = factory(\App\Models\Admin::class)->create();

        $settlement = factory(\App\Models\Settlement::class)->create();

        $employment  = factory(\App\Models\Employment::class)->create(['user_id'=> $settlement->loan->user->id]);

        $loggedSettlementCollection = factory(\App\Models\LoanWalletTransaction::class)->create([
            'settlement_id'=> $settlement->id,
            'amount'=> $settlement->amount,
            'loan_id'=> $settlement->loan->id,
            'user_id'=> $settlement->loan->user->id,
            'is_settlement'=>true 
        ]);

        $response  = $this->actingAs($this->admin, 'admin')->get(
            route('admin.settlement.confirm', ['reference'=>$settlement->reference])
        );
        
        $settlement->refresh();

        $this->assertTrue($settlement->status == 2);
        $user = $settlement->loan->user;
        Notification::assertSentTo([$user], SettlementApproved::class);
        
        // Assert there exists a loan collection IN - Transaction
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'settlement_id'=> $settlement->id,
            'amount'=> $settlement->amount,
            'is_settlement'=> true,
            'direction'=> 1
        ]);

        // Assert there exists a loan confirmation OUT - Transaction
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'settlement_id'=> $settlement->id,
            'amount'=> $settlement->amount,
            'is_settlement'=> true,
            'direction'=> 2
        ]);

        $response->assertStatus(302);

        //wallet balance is zero
        $this->assertEquals($settlement->loan->user->fresh()->loan_wallet, 0.0);


    }

    
    /**
     * Test admin can decline a settlement
     *@group Maintenance
     * @author Keania
     * @return void
     */
    public function testAdminCanDeclineSettlements()
    {
        PDF::shouldReceive('loadView')->andReturnSelf()
            ->getMock()
            ->shouldReceive('save')
            ->andReturn(true);

        Notification::fake();
        
        $this->admin = factory(\App\Models\Admin::class)->create();
        $settlement = factory(\App\Models\Settlement::class)->create();

        $data  =['status_message'=> 'Test Reasons'];

        $response  = $this->actingAs($this->admin, 'admin')->post(
            route('admin.settlement.decline', ['reference'=>$settlement->reference]), $data
        );

        $response->assertStatus(302);

        $settlement->refresh();

        $this->assertTrue(intval($settlement->status) == 3);

        $user = $settlement->loan->user;

        Notification::assertSentTo([$user], SettlementDeclined::class);
    }

    
    /**
     * Test admin can view a single settlement page
     *  @group Maintenance
     * @author Keania
     * @group badtest
     * @return void
     */
    public function testadminCanViewSingleSettlement()
    {
        $this->admin = factory(\App\Models\Admin::class)->create();
        $settlement = factory(\App\Models\Settlement::class)->create();

        $response  = $this->actingAs($this->admin, 'admin')->get(
            route('admin.settlement.view', ['reference'=>$settlement->reference])
        );

        $response->assertStatus(200);

        $response->assertSeeText('SETTLEMENT DOCUMENTS');
    }


    
    /**
     * Test that admin can create settlement
     * @group badtest
     * @group Maintenance
     * @author Keania
     */
    public function testadminCanCreateSettlement()
    {   
        $this->admin = factory(\App\Models\Admin::class)->create();
        $this->setupTest();

        $route = route(
            'admin.pay.settlement', ['reference'=>$this->loan->reference]
        );
   
        $this->mock->shouldReceive('getAuthorizationUrl')->once()->andReturnSelf();

        $this->mock->shouldReceive("redirectNow");

        $response = $this->actingAs($this->admin, 'admin')->post(
            $route, $this->data
        );

        // Our program is off to the gateway till he returns we wait
        $response->assertOk();

        
    }

    
    /**
     * Test that admin can make gateway call
     * @group Maintenance
     * @author Keania
     * @group badtest
     * @return void
     */
    public function testAdminGatewayCallbackWorks()
    {
        Notification::fake();

        PDF::shouldReceive('loadView')->andReturnSelf()
        ->getMock()
        ->shouldReceive('save')
        ->andReturn(true);

        $this->admin = factory(\App\Models\Admin::class)->create();
        $this->setupTest();

        $stud = $this->httpResponseFactory->createResponse('success');

        //enduring the return data is same
        $stud['data']['metadata'] = (array)json_decode($this->data['metadata']);

        $this->mock->shouldReceive('getPaymentData')->once()->andReturn($stud);

        $route = route('admin.settlement.payment_callback');

        //check that loan had no settlement
        $this->assertTrue(!isset($this->loan->settlement));

        // hit the callback handler for users
        $response = $this->actingAs($this->admin, 'admin')->get($route);

        // a settlement was created for this loan
        $this->loan->refresh();
        $this->assertTrue(isset($this->loan->settlement));

        $user = $this->loan->user;
        Notification::assertSentTo([$user], SettlementCreated::class);


    }

    
    /**
     * Ensures admin does not try to settle an already settled Loan
     * or A loan with pending settlement
     *@group Maintenance
     * @author Keania
     * @return void
     * @group badtest
     */
    public function testAdminCannotSettleAlreadySettledLoan()
    {
        $this->admin = factory(\App\Models\Admin::class)->create();
        $this->setupTest();

        
        $settlement = factory(\App\Models\Settlement::class)->create(
            ['loan_id'=>$this->loan->id]
        );

        $route = route(
            'admin.pay.settlement', ['reference'=>$this->loan->reference]
        );

        $response = $this->actingAs($this->admin, 'admin')->post(
            $route, $this->data
        );

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
