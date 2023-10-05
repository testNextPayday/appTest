<?php

namespace Tests\Feature\Affiliate;

use Tests\TestCase;
use App\Models\Settings;
use App\Events\LoanDisbursedEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\Investors\FundsDisbursedNotification;

class CommissionPaymentTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // create the repayment settings
        Settings::create(
            [
                'slug'=>'affiliate_repayment_commission',
                'name'=> 'Affiliate Repayment Commission',
                'value'=> 1.005
            ]
        );

        Settings::create(
            [
                'slug'=>'borrower_commission_rate',
                'name'=> 'Borrowers Commission Rate',
                'value'=> 1.005
            ]
        );

        Settings::create(
            [
                'slug'=>'supervisor_commission_rate',
                'name'=> 'Supervisor Commission Rate',
                'value'=> 1.005
            ]
        );


    }

    /**
     * Test user  with commission method disbursement
     * gets paid when loan is disbursed
     * 
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testBorrowerRecievesDisbursementCommission()
    {

        Event::fake();

        Notification::fake();

        $admin = factory(\App\Models\Admin::class)->create();

        $user = factory(\App\Models\User::class)->create();

        $oldWallet = $user->wallet; 

        // created a loan request
        $loan  = factory(\App\Models\Loan::class)->create(
            [
                'collector_type'=>get_class($user),
                'collector_id'=>$user->id
            ]
        );

        $disburse = route('admin.loans.disburse', ['loan'=> $loan->reference]);

        $response = $this->actingAs($admin, 'admin')->get($disburse);

        //dd($response->getSession());

        Event::assertDispatched(LoanDisbursedEvent::class);

        $investors = $loan->loanRequest->investors();

        Notification::assertSentTo($investors, FundsDisbursedNotification::class);

        $amount  = $loan->is_top_up ? $loan->disbursalAmount() : $loan->amount;
        // The affiliate wallet has increased by 
        $rate = Settings::borrowerCommissionRate();

        $commission = ($rate/100) * $amount;

        $newWallet = $commission + $oldWallet;
        
        $this->assertTrue($newWallet == $user->refresh()->wallet);
 
    }

    /**
     * Test affiliate with commission method disbursement
     * gets paid when loan is disbursed
     * 
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testAffiliateRecievesDisbursementCommission()
    {

        Event::fake();

        Notification::fake();

        $admin = factory(\App\Models\Admin::class)->create();

        $supervisor = factory(\App\Models\Affiliate::class)->create(
            ['commission_rate'=> 3, 'wallet'=> 0]
        );

        $affiliate = factory(\App\Models\Affiliate::class)->create(
            [
                'commission_rate'=>2.00, 
                'supervisor_id'=> $supervisor->id, 
                'supervisor_type'=> "App\Models\Affiliate"
            ]
        );

        $oldWallet = $affiliate->wallet; 


        $route = route(
            'admin.affiliate.settings', 
            ['affiliate'=>$affiliate->reference]
        );

        $data = [
            'loan_vissibility'=> 'view_all_loans', 
            'commission_method'=>'disbursement'
        ];

        // update the affiliate configurations
        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        // created a loan request
        $loan  = factory(\App\Models\Loan::class)->create(
            [
                'collector_type'=>'App\Models\Affiliate',
                'collector_id'=>$affiliate->id
            ]
        );

        $disburse = route('admin.loans.disburse', ['loan'=> $loan->reference]);

        $response = $this->actingAs($admin, 'admin')->get($disburse);

        Event::assertDispatched(LoanDisbursedEvent::class);

        $investors = $loan->loanRequest->investors();

        Notification::assertSentTo($investors, FundsDisbursedNotification::class);

        $amount  = $loan->is_top_up ? $loan->disbursalAmount() : $loan->amount;
        // The affiliate wallet has increased by 

        $commission = ($affiliate->commission_rate/100) * $amount;
       

        $newWallet = $commission + $oldWallet;
        
        $this->assertTrue($newWallet == $affiliate->refresh()->wallet);

        $supCommission  = (Settings::supervisorCommissionRate()/100) * $amount;

        $supervisor->refresh();

        $this->assertTrue($supervisor->wallet == $supCommission);
 
    }


    /**
     * Test affiliate with commission method repayment
     * gets paid when a plan is paid out
     * 
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testAffiliateRecievesRepaymentCommission()
    {

        // declare our plans here
        $plans = factory(\App\Models\RepaymentPlan::class, 3)->create(
            ['status'=>false, 'paid_out'=>false]
        );

        $plan = $plans->first();

        $admin = factory(\App\Models\Admin::class)->create();

        $supervisor = factory(\App\Models\Affiliate::class)->create(
            ['commission_rate'=> 3, 'wallet'=> 0]
        );

        $affiliate = factory(\App\Models\Affiliate::class)->create(
            [
                'commission_rate'=>2.00, 
                'supervisor_id'=> $supervisor->id, 
                'supervisor_type'=> "App\Models\Affiliate"
            ]
        );

        $oldWallet = $affiliate->wallet; 

        $route = route(
            'admin.affiliate.settings', 
            ['affiliate'=>$affiliate->reference]
        );

        $data = [
            'loan_vissibility'=> 'view_all_loans', 
            'commission_method'=>'repayment'
        ];

        // update the affiliate configurations
        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        // created a loan request

        $loan = $plan->loan;

        $loan->update(
            [
                'collector_type'=>'App\Models\Affiliate',
                'collector_id'=>$affiliate->id,
                'is_managed'=> false,
                'status'=> '1'
            ]
        );
        
        $investor = $loan->loanRequest->investors()->first();

        // mark plan as paid
        $plan->update(
            ['status'=>true, 'date_paid'=> now() ]
        );

        // run the investors settlement command
        Artisan::call('investors:settle');

        //check if the plan has been paid out
      
        $this->assertTrue($plan->refresh()->paid_out == true);


        $percentage = Settings::affiliateRepaymentCommission();

        // The affiliate wallet has increased by 

        $commission = ($percentage/100) * $plan->emi;

        $newWallet = intval($commission + $oldWallet);

        $this->assertTrue($newWallet == intval($affiliate->refresh()->wallet));

        // The affiliate wallet has increased by 

        $supCommission  = (Settings::supervisorCommissionRate()/100) * $plan->emi;

        $supervisor->refresh();

        $this->assertTrue($supervisor->wallet == $supCommission);
    }


    
    /**
     * An affiliate cannot receive commissions Twice
     * 
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testAffiliateCannotReceiveCommissionTwice()
    {
        Event::fake();

        Notification::fake();

        $plans = factory(\App\Models\RepaymentPlan::class, 3)->create(
            ['status'=>false, 'paid_out'=>false]
        );

        $plan = $plans->first();

        $admin = factory(\App\Models\Admin::class)->create();

        $affiliate = factory(\App\Models\Affiliate::class)->create(
            ['commission_rate'=>2.00]
        );

        $oldWallet = $affiliate->wallet; 

        $route = route(
            'admin.affiliate.settings', 
            ['affiliate'=>$affiliate->reference]
        );

        $data = [
            'loan_vissibility'=> 'view_all_loans', 
            'commission_method'=>'disbursement'
        ];

        // update the affiliate configurations
        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        // created a loan request
        $loan = $plan->loan;

        $loan->update(
            [
                'collector_type'=>'App\Models\Affiliate',
                'collector_id'=>$affiliate->id,
                'is_managed'=> false,
                'status'=> '1'
            ]
        );

        $disburse = route('admin.loans.disburse', ['loan'=> $loan->reference]);

        $response = $this->actingAs($admin, 'admin')->get($disburse);

        Event::assertDispatched(LoanDisbursedEvent::class);

        $investors = $loan->loanRequest->investors();

        Notification::assertSentTo($investors, FundsDisbursedNotification::class);

        $amount  = $loan->is_top_up ? $loan->disbursalAmount() : $loan->amount;
        // The affiliate wallet has increased by 

        $expectedBalance = ($affiliate->commission_rate/100) * $amount;

        $currentWallet = $expectedBalance + $oldWallet;

        $this->assertTrue($currentWallet == $affiliate->refresh()->wallet);

        // So now we change the affiliate configurations to see if he still gets commission
        $data = [
            'loan_vissibility'=> 'view_all_loans', 
            'commission_method'=>'repayment'
        ];

        // update the affiliate configurations
        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        // mark plan as paid
        $plan->update(
            ['status'=>true, 'date_paid'=> now() ]
        );

        // run the investors settlement command
        Artisan::call('investors:settle');

        //check if the plan has been paid out
      
        $this->assertTrue($plan->refresh()->paid_out == true);


        $percentage = Settings::affiliateRepaymentCommission();

        // The affiliate wallet has increased by 

        $expectedBalance = ($percentage/100) * $plan->emi;

        $newWallet = intval($expectedBalance + $currentWallet);

        // Check that the wallet did not get a new value
        $this->assertFalse($newWallet == intval($affiliate->refresh()->wallet));

        // Check that the wallet remains the same from previous transaction
        $this->assertTrue(
            intval($currentWallet) == intval($affiliate->refresh()->wallet)
        );
    }


    
}
