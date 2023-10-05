<?php

namespace Tests\Unit\Models;

use App\Models\Investor;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvestorTest extends TestCase
{
    use RefreshDatabase;
    
    protected $investor;

    public function setUp(): void
    {
        parent::setUp();  
        $this->investor = create(Investor::class, ['wallet' => 10000]);
    }
    
    /** @test **/
    public function an_investor_has_many_loan_funds()
    {
        $this->assertInstanceof(Collection::class, $this->investor->loanFunds);
    }
    
    /** @test **/
    public function an_investor_has_many_withdrawal_requests()
    {
        $this->assertInstanceof(Collection::class, $this->investor->withdrawals);   
    }
    
    /** @test **/
    public function an_investor_may_or_may_not_place_an_investment()
    {
        $amount = 5000;
        $this->assertTrue($this->investor->canPlaceInvestment($amount));
        
        $amount = 20000;
        $this->assertFalse($this->investor->canPlaceInvestment($amount));
    }
    
    /** @test **/
    public function an_investor_has_many_wallet_transactions()
    {
        $this->assertInstanceof(Collection::class, $this->investor->transactions);   
    }
    
    /** @test **/
    public function an_investor_has_many_bids()
    {
        $this->assertInstanceof(Collection::class, $this->investor->bids);   
    }
    
    /** @test **/
    public function an_investor_has_many_bank_details()
    {
        $this->assertInstanceof(Collection::class, $this->investor->banks);   
    }
}
