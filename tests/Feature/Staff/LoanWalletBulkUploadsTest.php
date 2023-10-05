<?php

namespace Tests\Feature\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanWalletBulkUploadsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Staff can upload payment
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testStaffUploadsGetsLogged()
    {
        $staff = factory(\App\Models\Staff::class)->create();

        // added repayment permisson
        $staff->update(['roles'=> 'manage_repayments']);
        $plan = factory(\App\Models\RepaymentPlan::class)->create();
        $faker = \Faker\Factory::create();

        $data = [
            [
                'borrower'=> $plan->id,
                'paid_amount'=> $plan->total_amount,
                'payment_method'=> $faker->randomElement(['Cash', 'PAYSTACK', 'Remita']),
                'collection_date'=> now(),
                'remark'=> 'Test Data'
            ]
        ];

        $url = route('staff.bulk-repayment');

        $response = $this->actingAs($staff, 'staff')->post($url, $data);
        
        $loan = $plan->loan;
        $user = $loan->user;
       
        // assert loan wallet entries exists
        $this->assertDatabaseHas('loan_wallet_transactions', [
            'is_logged'=>true,
            'user_id'=> $user->id,
            'loan_id'=> $loan->id,
            'plan_id'=> $plan->id,
            'amount'=> $plan->total_amount
        ]);

    }
}
