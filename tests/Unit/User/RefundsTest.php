<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Unit\Traits\RefundTest as RefundTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RefundsTest extends TestCase
{

    use RefreshDatabase;
    //use RefundTrait;
    /**
     * Test a user can create refund
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testUserCanMakeRefundRequestOnLoan()
    {
        $route = route('users.refund.request');

        $user = factory(\App\Models\User::class)->create();

        $loanWallet = $user->loan_wallet;

        $user->update(['email_verified'=>true, 'loan_wallet'=>2000]);

        $loan = factory(\App\Models\Loan::class)->create(['user_id'=>$user->id]);

        $data = [
            'loan_id'=>$loan->id, 
            'amount'=> 2000, 'reason'=>'Testing Reason', 
            'user_id'=>$user->id
        ];

        $response = $this->actingAs($user)->post($route, $data);

    
        $response->assertStatus(302);

        $this->assertDatabaseHas(
            'refunds', [
            'user_id'=>$user->id,
            'loan_id'=>$loan->id,
            'amount'=>$data['amount']]
        );
    }

    
    /**
     * Test that a user can view all refunds
     *  @group Maintenance
     *  @author Keania
     * @return void
     */
    public function testUserCanViewAllRefunds()
    {
        $route = route('users.refund.index');

        $user = factory(\App\Models\User::class)->create();

        $user->update(['email_verified'=>true]);

        $response = $this->actingAs($user)->get($route);

        $response->assertStatus(200);
    }


    
}
