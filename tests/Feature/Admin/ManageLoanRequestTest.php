<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\LoanRequest;
use App\Events\LoanRequestLiveEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageLoanRequestTest extends TestCase
{
  
   // use RefreshDatabase;
    
    /**
     * @group Maintenance
     * @author Keania
     * */
    public function test_admin_can_update_loan_request()
    {
      
       // get a particular loan Request
        $loanRequest =  factory(\App\Models\LoanRequest::class)->create();

        //test data
        $data = [
            'amount'=>25,
            'interest_percentage'=>7,
            'duration'=>5,
            'reference'=>$loanRequest->reference
        ];

        //get an admin to signin
        $admin = factory(\App\Models\Admin::class)->create();

        $this->actingAs($admin,'admin')->post(route('admin.loan-requests.update'),$data);
       
        
        $this->assertDatabaseHas('loan_requests',$data);
        
    }

     /**
     * @group Maintenance
     * @author Keania
     * */
    public function test_admin_cannot_update_cancelled_loan()
    {
         // get a particular loan Request
         $loanRequest =  factory(\App\Models\LoanRequest::class)->create([
             'status'=>3
         ]);

         //test data
         $data = [
             'amount'=>25,
             'interest_percentage'=>7,
             'duration'=>5,
             'reference'=>$loanRequest->reference
         ];
 
         //get an admin to signin
         $admin = factory(\App\Models\Admin::class)->create();
 
         $response = $this->actingAs($admin,'admin')->post(route('admin.loan-requests.update'),$data);
 
         $response->assertSessionHas('failure','Loan Request cannot be edited because it has been approved');
    }

     /**
     * @group Maintenance
     * @author Keania
     * */
    public function test_admin_cannot_update_active_loan()
    {
         // get a particular loan Request
         $loanRequest =  factory(\App\Models\LoanRequest::class)->create([
             'status'=>2
         ]);

         //test data
         $data = [
             'amount'=>25,
             'interest_percentage'=>7,
             'duration'=>5,
             'reference'=>$loanRequest->reference
         ];
 
         //get an admin to signin
         $admin = factory(\App\Models\Admin::class)->create();
 
         $response = $this->actingAs($admin,'admin')->post(route('admin.loan-requests.update'),$data);
 
         $response->assertSessionHas('failure','Loan Request cannot be edited because it has been approved');
    }


    
   
}
