<?php

namespace Tests\Unit\Investor;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WithdrawalTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCanSuccessfullyPlaceWithdrawals()
    {
        $investor = factory(\App\Models\Investor::class)->create();

        $data = ['amount'=> $investor->wallet];

        $response = $this->actingAs($investor,'investor')->post(route('investors.post.withdrawals'),$data);
        
        $response->assertStatus(200);

        $response->assertHasJson(['success'=>'Request placed successfully']);
    }
}
