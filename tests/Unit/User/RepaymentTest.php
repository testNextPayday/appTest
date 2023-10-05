<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RepaymentTest extends TestCase
{

    use RefreshDatabase;

    /**
     * This test that sending valid repayment plan ids Update works
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testUpdatePlansWork()
    {
        $route = route('users.update-repaymentplans');

        $loan = factory(\App\Models\Loan::class)->create(['status'=>'1']);

        $user = factory(\App\Models\User::class)->create();

        $plans = factory(\App\Models\RepaymentPlan::class, 3)->create(
            ['loan_id'=>$loan->id]
        );

        $ids = array_values($plans->pluck('id')->toArray());

        $data = [
            'paymentIds'=>$ids
        ];

        $response  = $this->actingAs($user)->post($route, $data);
       
        $response->assertStatus(200);

        foreach ($plans as $index=>$plan) {
            $plan->refresh();

            $this->assertTrue($plan->status == true);
        }
    }

    
    /**
     * Test that sending an invalid plan everything rolls back
     *@group Maintenance
     * @author Keania
     * @return void
     */
    public function testPlanNotExistFails()
    {
        
        $route = route('users.update-repaymentplans');

        $loan = factory(\App\Models\Loan::class)->create(['status'=>'1']);

        $user = factory(\App\Models\User::class)->create();

        $plans = factory(\App\Models\RepaymentPlan::class, 3)->make(
            ['loan_id'=>$loan->id]
        );

        $ids = array_values($plans->pluck('id')->toArray());

        $data = [
            'paymentIds'=>$ids
        ];

        $response  = $this->actingAs($user)->post($route, $data);

        $response->assertStatus(422);

    }
}
