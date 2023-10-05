<?php

namespace Tests\Feature\Staff;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StaffRefundApprovalTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffCannotViewPending()
    {
       Event::fake();

       $staff = factory(\App\Models\Staff::class)->create();

       $route = route('staff.refund.pending');
        
       $response = $this->actingAs($staff, 'staff')->get($route);

       $response->assertStatus(302);
    }

    /**
     * Testing Permissioned Staff can view pending refunds
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testPermissionedStaffCanViewPending()
    {
       Event::fake();

       $staff = factory(\App\Models\Staff::class)->create();

       $staff->update(['roles'=>'manage_approve_refunds']);

       $refund = factory(\App\Models\Refund::class)->create();

       $loan = $refund->loanInfo;

       $route = route('staff.refund.pending');
        
       $response = $this->actingAs($staff, 'staff')->get($route);
      
       $response->assertStatus(200);
        
       $response->assertSeeText($loan->reference);
    }


    /**
     * Testing Permissioned Staff can view pending refunds
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testPermissionedStaffCanUpdateRefund()
    {
       Event::fake();

       $staff = factory(\App\Models\Staff::class)->create();

       $staff->update(['roles'=>'manage_approve_refunds']);

       $refund = factory(\App\Models\Refund::class)->create();

       $route = route('staff.refund.update', ['id' => $refund->id, 'status' => 2]);
        
       $response = $this->actingAs($staff, 'staff')->patch($route);
        
       $refund->refresh();

       $this->assertTrue($refund->status == 2);
    }



}
