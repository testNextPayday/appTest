<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManagedLoansCardSweeperTest extends TestCase
{

    use RefreshDatabase;
    /**
     * Test we can fetch sweepable managed loans
     * @group Maintenance
     * 
     * @author Keania
     *
     * @return void
     */
    public function testSweepableManagedLoans()
    {
       $loans = factory(\App\Models\Loan::class, 4)->create(
           ['is_managed'=>1]
       );

       $admin = factory(\App\Models\Admin::class)->create();

       $route = route('admin.loans.managed-sweepable');

       $response = $this->actingAs($admin, 'admin')->get($route);

       $response->assertStatus(200);
    }

    
    /**
     * Test we can send plan for sweeping
     ** @group Maintenance
      * 
     * @author Keania
     * @return void
     */
    public function testSweepPlan()
    {
        $plan = factory(\App\Models\RepaymentPlan::class, 4)->create()->first();
 
        $admin = factory(\App\Models\Admin::class)->create();
 
        $route = route('admin.loans.managed.sweep-plan', ['plan'=>$plan->id]);
 
        $response = $this->actingAs($admin, 'admin')->post($route);
 
        $response->assertStatus(200);
    }

    
    /**
     * Test we can get the batch sweeping status of loans
     ** @group Maintenance
      * 
     * @author Keania
     * @return void
     */
    public function testCanGetBatchSweepingStatus()
    {
        $plan = factory(\App\Models\RepaymentPlan::class, 4)->create()->first();
 
        $admin = factory(\App\Models\Admin::class)->create();
 
        $route = route('admin.loans.managed.sweep-plan', ['plan'=>$plan->id]);
 
        $response = $this->actingAs($admin, 'admin')->post($route);
 
        $response->assertStatus(200);

        $routeSweepStatus = route('admin.managed.sweep-status');

        $response2 =  $this->actingAs($admin, 'admin')->get($routeSweepStatus);


    }


    

}
