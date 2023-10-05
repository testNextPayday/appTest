<?php

namespace Tests\Feature\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageWithdrawalTest extends TestCase
{
    /**
     * Permissioned Staff can view pending witrhdrawals
     *
     * @return void
     */
    public function testPermissionedStaffViewPendingWithdrawals()
    {
       $staff  = factory(\App\Models\Staff::class)->create();

       $staff->update(['roles'=> 'manage_withdrawal_approval']);

       $withdrawal = factory(\App\Models\WithdrawalRequest::class)->create();

       $route = route('staff.withdrawals.pending');

       $response  = $this->actingAs($staff, 'staff')->get($route);

       $response->assertStatus(200);

       $response->assertSeeText($withdrawal->reference);
    }


      /**
     *  Staff cannot view pending witrhdrawals
     *
     * @return void
     */
    public function testStaffCannotViewPendingWithdrawals()
    {
       $staff  = factory(\App\Models\Staff::class)->create();

       $withdrawal = factory(\App\Models\WithdrawalRequest::class)->create();

       $route = route('staff.withdrawals.pending');

       $response  = $this->actingAs($staff, 'staff')->get($route);

       $response->assertStatus(302);

    }

      /**
     * Permissioned Staff can approved withdrawals
     *
     * @return void
     */
    public function testPermissionedStaffCanApproveWithdrawals()
    {
       $staff  = factory(\App\Models\Staff::class)->create();

       $staff->update(['roles'=> 'manage_withdrawal_approval']);

       $withdrawal = factory(\App\Models\WithdrawalRequest::class)->create(['status'=>1]);

       $route = route('staff.withdrawals.approve', ['request_id'=>$withdrawal->id]);

       $response  = $this->actingAs($staff, 'staff')->get($route);

      $withdrawal->refresh();

       $response->assertSeeText($withdrawal->status == 2);

    }


     /**
     * Staff cannot approved withdrawals
     *
     * @return void
     */
    public function testStaffCannotApproveWithdrawals()
    {
       $staff  = factory(\App\Models\Staff::class)->create();


       $withdrawal = factory(\App\Models\WithdrawalRequest::class)->create(['status'=>1]);

       $route = route('staff.withdrawals.approve', ['request_id'=>$withdrawal->id]);

       $response  = $this->actingAs($staff, 'staff')->get($route);

       $response->assertStatus(302);


    }


      /**
     * Permissioned Staff can decline withdrawals
     *
     * @return void
     */
    public function testPermissionedStaffCanDeclineWithdrawals()
    {
       $staff  = factory(\App\Models\Staff::class)->create();

       $staff->update(['roles'=> 'manage_withdrawal_approval']);

       $withdrawal = factory(\App\Models\WithdrawalRequest::class)->create(['status'=>1]);

       $route = route('staff.withdrawals.decline', ['request_id'=>$withdrawal->id]);

       $response  = $this->actingAs($staff, 'staff')->get($route);

      $withdrawal->refresh();

       $response->assertSeeText($withdrawal->status == 3);

    }

     /**
     * Staff cannot decline withdrawals
     *
     * @return void
     */
    public function testStaffCannotDeclineWithdrawals()
    {
       $staff  = factory(\App\Models\Staff::class)->create();


       $withdrawal = factory(\App\Models\WithdrawalRequest::class)->create(['status'=>1]);

       $route = route('staff.withdrawals.approve', ['request_id'=>$withdrawal->id]);

       $response  = $this->actingAs($staff, 'staff')->get($route);

       $response->assertStatus(302);


    }
}
