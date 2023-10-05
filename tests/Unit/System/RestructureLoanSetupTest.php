<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use App\Models\Loan;
use App\Models\Admin;
use App\Channels\TermiiChannel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Users\LoanSetupNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestructureLoanSetupTest extends TestCase
{
    /**
     * @group Maintenance
     * @author Keania
     *  
     * @desc Given any active loan setup works for it
     */
    public function testSetupWorksForAnyActiveLoan()
    {

        Notification::fake();
        
       $loan = factory(\App\Models\Loan::class)->create([
           'status'=>'1'
       ]);

       $bankDetails = factory(\App\Models\BankDetail::class)->create([
           'owner_id'=>$loan->user->id,
           'owner_type'=>'App\Models\User'
       ]);

       $setupData = [

           'collection_plan'=>300,
           'collection_plan_secondary'=>200
       ];


       $admin = factory(\App\Models\Admin::class)->create();

      

       $response = $this->actingAs($admin,'admin')->post(route('admin.loans.setup.loan',['loan'=>$loan->reference]), $setupData);
      
        // the loan's collection plan has changed
        $loan->refresh(); // update the loan from database
       
        $this->assertTrue($loan->collection_plan == $setupData['collection_plan']);

       Notification::assertSentTo(
           
           $loan->user,
           LoanSetupNotification::class
        );
        
       $response->assertStatus(200);

       $response->assertJson(['success'=>'Setup was successful']);

    }



}
