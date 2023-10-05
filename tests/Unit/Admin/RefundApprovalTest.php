<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Refund;
use App\Models\Admin;

class RefundApprovalTest extends TestCase
{   
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *  @author Alisi
      *  @group BadMaintenance
     * @return void
     */
    public function test_admin_refund_approval()
    {   $refund = factory(\App\Models\Refund::class)->create();
        $data  = [
            'id'      => $refund->id,
            'status'  => 1,
        ];
        //get an admin to signin
        $admin = factory(\App\Models\Admin::class)->create();

        $response = $this->actingAs($admin,'admin')->patch(route('admin.update.refund',['id' => $refund->id,'status'=> 0]),$data);
        $this->assertDatabaseHas('refunds', $data);
        $response->assertStatus(302);
        // $response->assertRedirect(route('admin.refund.pending'));

    }
}
