<?php

namespace Tests\Feature\Investor;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\WithdrawalRequest;
use App\Models\Investor;

class RequestsWithdrawalTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test **/
    public function an_authenticated_investor_can_place_a_withdrawal_request()
    {
        $investor = create(Investor::class, ['wallet' => 50000]);
        $this->signIn($investor, 'investor');
        
        $request =  ['amount' => 20000];
        
        $this->post('investor/withdrawals', $request);
        
        $this->assertDatabaseHas('withdrawal_requests', [
            'amount' => $request['amount'],
            'requester_id' => $investor->id
        ]);
        
    }
    
    /** @test **/
    public function an_authenticated_investor_can_view_all_his_withdrawal_requests()
    {
        $investor = create(Investor::class);
        
        $request =  create(WithdrawalRequest::class, ['requester_id' => $investor->id]);
        
        $this->signIn($investor, 'investor');
        
        $this->get('investor/withdrawals')
            ->assertSee($request->reference);
    }
}
