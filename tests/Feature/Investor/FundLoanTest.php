<?php

namespace Tests\Feature\Investor;

use Tests\TestCase;
use App\Models\Investor;
use App\Models\LoanFund;

use App\Models\LoanRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FundLoanTest extends TestCase
{
    use RefreshDatabase;
    
   
    /**
     * Test can view available Loan Request
     * @group Maintenance
     * @group Inherited
     *
     * @return void
     */
    public function test_authenticated_investor_can_view_available_loan_requests()
    {
        $activeRequest = create(LoanRequest::class, ['status' => 2]);
        $inactiveRequest = create(LoanRequest::class, ['status' => 1]);
        $this->signIn(null, 'investor');
        
        $this->get("/requests/marketplace")
            ->assertSee(strtoupper($activeRequest->reference))
            ->assertDontSee($inactiveRequest->reference);
    }
    
        
    /**
     * Test Loan Funding is successful
     * @group Maintenance
     *@group Inherited
     * @return void
     */
    public function test_authenticated_investor_can_fund_an_active_loan_request()
    {
        $loanRequest = create(LoanRequest::class);
    
        $fund = ['percentage' => 10];
        
        $investor = create(Investor::class, ['wallet' => 20000]);
        
        $this->signIn($investor, 'investor');
        Notification::fake();
        $this->post("/requests/" . $loanRequest->reference . "/fund", $fund)
            ->assertStatus(200);
            
        $this->assertEquals(90, $loanRequest->fresh()->percentage_left);
        
        $amount = $loanRequest->amount * $fund['percentage'] / 100;
        
        $this->assertEquals(20000 - $amount, $investor->fresh()->wallet);
        //$this->assertEquals($amount, $loanRequest->user->escrow);
    }
    
        
    /**
     * Test cannot fraud Nextpayday
     * @group Maintenance
     *@group Inherited
     * @return void
     */
    public function test_investor_cannot_fund_more_than_in_wallet()
    {
        $loanRequest = create(LoanRequest::class, ['amount' => 100000]);
    
        $fund = ['percentage' => 10];
        
        $investor = create(Investor::class, ['wallet' => 1000]);
        
        $this->signIn($investor, 'investor');
        
        $this->post("/requests/" . $loanRequest->reference . "/fund", $fund)
            ->assertStatus(400);
            
        $this->assertEquals(100, $loanRequest->fresh()->percentage_left);
        
        $this->assertEquals(1000, $investor->fresh()->wallet);
    }
    
        
    /**
     * Test can view funded loans
     * @group Maintenance
     *@group Inherited
     * @return void
     */
    public function test_an_authenticated_investor_can_see_a_list_of_his_loan_funds()
    {
        $john = create(Investor::class);
        
        $johnsFund = create(LoanFund::class, ['investor_id' => $john->id]);
        $otherFund = create(LoanFund::class);
        
        $this->signIn($john, 'investor');
        
            
        $this->get("/funds")
            ->assertSee(strtoupper($johnsFund->loanRequest->reference))
            ->assertDontSee($otherFund->loanRequest->reference);
    }
    
        
    /**
     * Test can view loan funds
     * 
     * @group Maintenance
     *@group Inherited
     * @return void
     */
    public function test_an_authenticated_investor_can_view_a_loan_fund()
    {
        $john = create(Investor::class);
        
        $fund = create(LoanFund::class, ['investor_id' => $john->id]);
        
        $this->signIn($john, 'investor');
        
        $this->get("funds/{$fund->reference}")
            ->assertSee($fund->reference);
    }
    
        
    /**
     * Test can place loans on the market
     * @group Maintenance
     *@group Inherited
     * @return void
     */
    public function test_an_authenticated_investor_can_place_an_active_fund_on_transfer()
    {
        $john = create(Investor::class);
        
        $fund = create(LoanFund::class, ['investor_id' => $john->id, 'status' => 2]);
        
        $this->signIn($john, 'investor');
        
        $data = ['amount' => 10000];
        
        $this->post("funds/{$fund->reference}/transfer", $data)
            ->assertStatus(200);
        
        $this->assertNotNull($fund->fresh()->sale_amount);
        $this->assertEquals(10000, $fund->fresh()->sale_amount);
        $this->assertEquals(4, $fund->fresh()->status);
    }
}
