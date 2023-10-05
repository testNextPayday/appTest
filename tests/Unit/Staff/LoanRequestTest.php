<?php

namespace Tests\Unit\Staff;

use Tests\TestCase;
use App\Events\LoanRequestLiveEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\Users\LoanRequestLiveNotification;
use App\Notifications\Users\LoanRequestUpdatedNotification;
use App\Notifications\Users\LoanRequestApprovalNotification;

class LoanRequestTest extends TestCase
{
    /**
     * Test can view pending loan request
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffCanViewPending()
    {
       
        $staff = factory(\App\Models\Staff::class)->create();

        $staff->update(['roles'=>'manage_loan_request']);

        $url = route('staff.loan-requests.pending');
        
        $response =  $this->actingAs($staff, 'staff')->get($url);

        $response->assertStatus(200);
    }

    
    /**
     * Test  a privileged user can approve loan request
     *@group Maintenance
     * @author Keania
     * @return void
     */
    public function testCanApproveLoanRequest()
    {
        Notification::fake();

        Event::fake();
        
        $staff = factory(\App\Models\Staff::class)->create();

        $faker = \Faker\Factory::create();

        $loanRequest = factory(\App\Models\LoanRequest::class)->create();

        $staff->update(['roles'=>'manage_loan_request']);

        $url = route(
            'staff.loan-requests.approve', 
            ['loanRequest'=>$loanRequest->id]
        );

        $data = [
            'request_id'=>$loanRequest->id,
            'collection_plan'=>'0',
            'risk_rating'=> $faker->randomElement([5, 4, 3, 2, 1])
        ]; 
        
        $response =  $this->actingAs($staff, 'staff')->post($url, $data);

        $response->assertStatus(302);

        $loanRequest->refresh();

        $this->assertTrue($loanRequest->status == 2);

        Notification::assertSentTo(
            $loanRequest->user, LoanRequestLiveNotification::class
        );

        Event::assertDispatched(LoanRequestLiveEvent::class);

        $response->assertSessionHas(['success'=>'Request has been approved']);
    }



    /**
     * Test  a privileged user can decline loan request
     *@group Maintenance
     * @author Keania
     * @return void
     */
    public function testCanDeclineLoanRequest()
    {
        Notification::fake();

        $staff = factory(\App\Models\Staff::class)->create();

        $faker = \Faker\Factory::create();

        $loanRequest = factory(\App\Models\LoanRequest::class)->create();

        $staff->update(['roles'=>'manage_loan_request']);

        $url = route(
            'staff.loan-requests.decline', 
            ['reference'=>$loanRequest->reference]
        );

        $response =  $this->actingAs($staff, 'staff')->get($url);

        $response->assertStatus(302);

        $loanRequest->refresh();


        Notification::assertSentTo(
            $loanRequest->user,
            LoanRequestApprovalNotification::class
        );
        $this->assertTrue($loanRequest->status == 6);

        $response->assertSessionHas(['success'=>'Loan request has been declined']);
    }


    /**
     * Test  a privileged user can edit loan request
     *@group Maintenance
     * @author Keania
     * @return void
     */
    public function testCanUpdateLoanRequest()
    {
        Notification::fake();

        $staff = factory(\App\Models\Staff::class)->create();

        $faker = \Faker\Factory::create();

        $loanRequest = factory(\App\Models\LoanRequest::class)->create();

        $staff->update(['roles'=>'manage_loan_request']);

        $url = route('staff.loan-requests.update');

        $amount = $faker->numberBetween($min=50000, $max=10000);

        $rate = $faker->numberBetween($min=4, $max=10);

        $tenure = $faker->numberBetween($min=4, $max=10);

        $data = [
            'interest_percentage'=>$rate,
            'duration'=>$tenure,
            'amount'=> $amount,
            'reference'=>$loanRequest->reference
        ]; 
        
        $response =  $this->actingAs($staff, 'staff')->post($url, $data);

        $response->assertStatus(302);

        $loanRequest->refresh();

        $expectedValue = pmt($amount, $rate, $tenure) + $loanRequest->mgt_fee();

        $this->assertTrue($loanRequest->emi == $expectedValue);

        Notification::assertSentTo(
            $loanRequest->user,
            LoanRequestUpdatedNotification::class
        );

        $response->assertSessionHas(['success'=>'Loan request has been successfully edited']);
    }


    
}
