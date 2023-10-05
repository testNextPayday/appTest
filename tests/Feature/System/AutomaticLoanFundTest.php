<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use App\Events\LoanRequestLiveEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\Users\LoanRequestLiveNotification;

class AutomaticLoanFundTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testLoanRequestLiveEventFires()
    {
        Notification::fake();
        Event::fake();
        $loanRequest = factory(\App\Models\LoanRequest::class)->create();
        $admim = factory(\App\Models\Admin::class)->create();

        $this->be($admim,'admin')->post(route('admin.loan-requests.approve'),[
            'request_id'=>$loanRequest->id,
            'risk_rating'=>'5'
        ]);
        Notification::assertSentTo(
            $loanRequest->user,
            LoanRequestLiveNotification::class,
            function ($notification, $channels) use ($loanRequest) {
                return $notification->loanRequest->id === $loanRequest->id;
            }
        );
        Event::assertDispatched(LoanRequestLiveEvent::class,function($e) use($loanRequest){
            return $e->loanRequest->id == $loanRequest->id;
        });
    }

    /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testAutomaticFunderFundsSingleInvestorLoan()
    {
        Notification::fake();
        $loanRequest = factory(\App\Models\LoanRequest::class)->create();
        $admim = factory(\App\Models\Admin::class)->create();
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount * 2,
            'auto_invest'=>true,
            'loan_fraction'=>100,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id]),
            'loan_tenors'=>6
        ]);
            
        $response = $this->be($admim,'admin')->post(route('admin.loan-requests.approve'),[
            'request_id'=>$loanRequest->id,
            'risk_rating'=>'5'
        ]);
        
        Notification::assertSentTo(
            $loanRequest->user,
            LoanRequestLiveNotification::class,
            function ($notification, $channels) use ($loanRequest) {
                return $notification->loanRequest->id === $loanRequest->id;
            }
        );

        
        // The loan Request was funded
        $loanRequest = $loanRequest->fresh();
        $this->assertTrue($loanRequest->status == 4);
        $this->assertTrue($loanRequest->percentageLeft == 0);

        $this->assertDatabaseHas('loan_funds',[
            'investor_id'=>$investor->id,
            'request_id'=>$loanRequest->id,
            'percentage'=>100
        ]);



    }


    /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testAutomaticFunderFundsDoubleInvestorLoan()
    {
        Notification::fake();
        $loanRequest = factory(\App\Models\LoanRequest::class)->create();
        $admim = factory(\App\Models\Admin::class)->create();

        $investor1 = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount / 2 ,
            'auto_invest'=>true,
            'loan_fraction'=>50,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id]),
            'loan_tenors'=>6
        ]);

        $investor2 = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount / 2 ,
            'auto_invest'=>true,
            'loan_fraction'=>50,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id]),
            'loan_tenors'=>6
        ]);
            
        $response = $this->be($admim,'admin')->post(route('admin.loan-requests.approve'),[
            'request_id'=>$loanRequest->id,
            'risk_rating'=>'5'
        ]);
      
        Notification::assertSentTo(
            $loanRequest->user,
            LoanRequestLiveNotification::class,
            function ($notification, $channels) use ($loanRequest) {
                return $notification->loanRequest->id === $loanRequest->id;
            }
        );
        
         // The loan Request was funded
         $loanRequest = $loanRequest->fresh();
         $this->assertTrue($loanRequest->status == 4);
         $this->assertTrue($loanRequest->percentageLeft == 0);

        $this->assertDatabaseHas('loan_funds',[
            'investor_id'=>$investor1->id,
            'request_id'=>$loanRequest->id,
            'percentage'=>50
        ]);

        $this->assertDatabaseHas('loan_funds',[
            'investor_id'=>$investor2->id,
            'request_id'=>$loanRequest->id,
            'percentage'=>50
        ]);
        }



         /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testAutomaticFunderFundsQuaterInvestorLoan()
    {
        Notification::fake();
        $loanRequest = factory(\App\Models\LoanRequest::class)->create();
        $admim = factory(\App\Models\Admin::class)->create();

        $investor1 = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount / 4 ,
            'auto_invest'=>true,
            'loan_fraction'=>25,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id]),
            'loan_tenors'=>6
        ]);

        $investor2 = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount / 4 ,
            'auto_invest'=>true,
            'loan_fraction'=>25,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id]),
            'loan_tenors'=>6
        ]);

        $investor3 = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount / 4 ,
            'auto_invest'=>true,
            'loan_fraction'=>25,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id]),
            'loan_tenors'=>6
        ]);

        $investor4 = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount / 4 ,
            'auto_invest'=>true,
            'loan_fraction'=>25,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id]),
            'loan_tenors'=>6
        ]);
            
        $response = $this->be($admim,'admin')->post(route('admin.loan-requests.approve'),[
            'request_id'=>$loanRequest->id,
            'risk_rating'=>'5'
        ]);

        Notification::assertSentTo(
            $loanRequest->user,
            LoanRequestLiveNotification::class,
            function ($notification, $channels) use ($loanRequest) {
                return $notification->loanRequest->id === $loanRequest->id;
            }
        );
        
         // The loan Request was funded
         $loanRequest = $loanRequest->fresh();
         $this->assertTrue($loanRequest->status == 4);
         $this->assertTrue($loanRequest->percentageLeft == 0);

        $this->assertDatabaseHas('loan_funds',[
            'investor_id'=>$investor1->id,
            'request_id'=>$loanRequest->id,
            'percentage'=>25
        ]);

        $this->assertDatabaseHas('loan_funds',[
            'investor_id'=>$investor2->id,
            'request_id'=>$loanRequest->id,
            'percentage'=>25
        ]);


        $this->assertDatabaseHas('loan_funds',[
            'investor_id'=>$investor3->id,
            'request_id'=>$loanRequest->id,
            'percentage'=>25
        ]);

        $this->assertDatabaseHas('loan_funds',[
            'investor_id'=>$investor4->id,
            'request_id'=>$loanRequest->id,
            'percentage'=>25
        ]);

        }



         /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testAutomaticFunderFundsWhenInvestorHasRangeofEmployers()
    {
        Notification::fake();
        $loanRequest = factory(\App\Models\LoanRequest::class)->create();
        $admim = factory(\App\Models\Admin::class)->create();
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount * 2,
            'auto_invest'=>true,
            'loan_fraction'=>100,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id,2,8,12]),
            'loan_tenors'=>6
        ]);
            
        $response = $this->be($admim,'admin')->post(route('admin.loan-requests.approve'),[
            'request_id'=>$loanRequest->id,
            'risk_rating'=>'5'
        ]);
        
        Notification::assertSentTo(
            $loanRequest->user,
            LoanRequestLiveNotification::class,
            function ($notification, $channels) use ($loanRequest) {
                return $notification->loanRequest->id === $loanRequest->id;
            }
        );

         // The loan Request was funded
         $loanRequest = $loanRequest->fresh();
         $this->assertTrue($loanRequest->status == 4);
         $this->assertTrue($loanRequest->percentageLeft == 0);

        $this->assertDatabaseHas('loan_funds',[
            'investor_id'=>$investor->id,
            'request_id'=>$loanRequest->id,
            'percentage'=>100
        ]);

    }


     /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testAutomaticFunderFailsWhenNoAutoInvestInvestor()
    {
        Notification::fake();
        $loanRequest = factory(\App\Models\LoanRequest::class)->create();
        $admim = factory(\App\Models\Admin::class)->create();
        $investor = factory(\App\Models\Investor::class)->create([
            'wallet'=>$loanRequest->amount * 2,
            'auto_invest'=>false,
            'loan_fraction'=>100,
            'employer_loan'=>json_encode([$loanRequest->employment->employer_id]),
            'loan_tenors'=>6
        ]);
            
        $response = $this->be($admim,'admin')->post(route('admin.loan-requests.approve'),[
            'request_id'=>$loanRequest->id,
            'risk_rating'=>'5'
        ]);
        Notification::assertSentTo(
            $loanRequest->user,
            LoanRequestLiveNotification::class,
            function ($notification, $channels) use ($loanRequest) {
                return $notification->loanRequest->id === $loanRequest->id;
            }
        );
        
        // loan request is active but unfunded
        $this->assertTrue($loanRequest->fresh()->status == 2);



    }




        
}
