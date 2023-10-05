<?php

namespace Tests\Unit\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateBillTest extends TestCase
{
    /**
     * An unpermitted staff cannot see manage bills navigation
     * 
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testUnPermittedStaffCannotSeeManageBillNav()
    {
        $staff = factory(\App\Models\Staff::class)->create();

        $route = route('staff.dashboard');

        $response = $this->actingAs($staff, 'staff')->get($route);

        $response->assertDontSeeText('Manage Bills');
    }

    /**
     * Permitted staff will sees manage bills
     * 
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testPermittedStaffCanSeeManageBillNav()
    {
        $staff = factory(\App\Models\Staff::class)->create();

        $staff->update(['roles'=>'manage_bills']);

        $route = route('staff.dashboard');

        $response = $this->actingAs($staff, 'staff')->get($route);

        $response->assertSeeText('Manage Bills');
    }


    
}
