<?php

namespace Tests\Feature\Investor;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\LoanFund;
use App\Models\Investor;
use App\Models\Bid;

class PurchaseFundsTest extends TestCase
{
    
    use RefreshDatabase;
    
    /** @test **/
    public function an_authenticated_investor_can_view_available_funds_in_market_that_are_not_his_own()
    {
        $investor = create(Investor::class);
        
        $investorFundOnSale = create(LoanFund::class, ['status' => 4, 'investor_id' => $investor->id]);
        $otherInvestorFundOnSale = create(LoanFund::class, ['status' => 4]);
        
        $investorFundNotOnSale = create(LoanFund::class, ['status' => 2, 'investor_id' => $investor->id]);
        $otherInvestorFundNotOnSale = create(LoanFund::class, ['status' => 2]);
        
        $this->signIn($investor, 'investor');
        
        $this->get("/funds/market")
            ->assertSee($otherInvestorFundOnSale->reference)
            ->assertDontSee($investorFundOnSale->reference)
            ->assertDontSee($investorFundNotOnSale->reference)
            ->assertDontSee($otherInvestorFundNotOnSale->reference);
    }
    
    /** @test **/
    public function an_authenticated_investor_can_place_a_bid_on_an_available_asset()
    {
        $asset = create(LoanFund::class, ['status' => 4, 'sale_amount' => 10000]);
        
        $investor = create(Investor::class, ['wallet' => 20000, 'escrow' => 0]);
        
        $this->signIn($investor, 'investor');
        
        $this->post("funds/{$asset->reference}/market/bid", ['amount' => 10000])
            ->assertStatus(200);
            
        $this->assertEquals(10000, $investor->fresh()->wallet);
        $this->assertEquals(10000, $investor->fresh()->escrow);
        
        $this->assertCount(1, $asset->bids);
    }
    
    /** @test **/
    public function an_authenticated_investor_can_view_his_bids()
    {
        $investor = create(Investor::class);
        $bid = create(Bid::class, ['investor_id' => $investor->id]);
        
        $this->signIn($investor, 'investor');
        
        $expectation = number_format($bid->amount, 2);
        $this->get("bids")->assertSee("{$expectation}");
    }
    
    /** @test **/
    public function an_authenticated_investor_can_accept_a_bid_on_his_asset()
    {
        $investor = create(Investor::class, ['wallet' => 20000, 'escrow' => 0]);
        $asset = create(LoanFund::class, [
            'status' => 4, 'sale_amount' => 10000, 'investor_id' => $investor->id
        ]);
        
        $bidderOne = create(Investor::class, ['escrow' => 20000]);
        $bidOne = create(Bid::class, [
            'fund_id' => $asset->id, 'amount' => 10000, 'investor_id' => $bidderOne->id
        ]);
        
        $bidderTwo = create(Investor::class, ['escrow' => 20000]);
        $bidTwo = create(Bid::class, [
            'fund_id' => $asset->id, 'amount' => 20000, 'investor_id' => $bidderTwo->id
        ]);
        
        $bidderThree = create(Investor::class, ['escrow' => 20000]);
        $bidThree = create(Bid::class, [
            'fund_id' => $asset->id, 'amount' => 5000, 'investor_id' => $bidderThree->id
        ]);
        
        $this->signIn($investor, 'investor');
        
        $this->post("bids/{$bidOne->id}/accept")
            ->assertStatus(200);
        
        $this->assertEquals(2, $bidOne->fresh()->status);
        $this->assertEquals(5, $asset->fresh()->status);
        $this->assertEquals(4, $bidTwo->fresh()->status);
        $this->assertEquals(4, $bidThree->fresh()->status);
        
        $this->assertEquals(30000, $investor->fresh()->wallet);
        
        $this->assertEquals(10000, $bidderOne->fresh()->escrow);
        
        $this->assertEquals(0, $bidderTwo->fresh()->escrow);
        $this->assertEquals(20000, $bidderTwo->fresh()->wallet);
        
        $this->assertEquals(15000, $bidderThree->fresh()->escrow);
        $this->assertEquals(5000, $bidderThree->fresh()->wallet);
        
    }
    
    /** @test **/
    public function an_authenticated_investor_can_reject_a_bid_on_his_asset()
    {
        $investor = create(Investor::class, ['wallet' => 20000, 'escrow' => 0]);
        $asset = create(LoanFund::class, [
            'status' => 4, 'sale_amount' => 10000, 'investor_id' => $investor->id
        ]);
        
        $bidderOne = create(Investor::class, ['escrow' => 20000]);
        $bidOne = create(Bid::class, [
            'fund_id' => $asset->id, 'investor_id' => $bidderOne->id, 'amount' => 5000
        ]);
        
        $bidderTwo = create(Investor::class, ['escrow' => 20000]);
        $bidTwo = create(Bid::class, [
            'fund_id' => $asset->id, 'investor_id' => $bidderTwo->id, 'amount' => 10000
        ]);
        
        $this->signIn($investor, 'investor');
        
        $this->post("bids/{$bidOne->id}/reject")
            ->assertStatus(200);
        
        $this->assertEquals(3, $bidOne->fresh()->status);
        $this->assertEquals(4, $asset->fresh()->status);
        $this->assertEquals(1, $bidTwo->fresh()->status);
        $this->assertEquals(20000, $investor->fresh()->wallet);
        
        $this->assertEquals(5000, $bidderOne->fresh()->wallet);
        $this->assertEquals(15000, $bidderOne->fresh()->escrow);
        
        $this->assertEquals(20000, $bidderTwo->fresh()->escrow);
    }
    
    /** @test **/
    // public function an_authenticated_investor_can_update_his_asset_sale_amount()
    // {
        
    // }
    
    /** @test **/
    // public function an_authenticated_investor_can_update_his_bid_amount()
    // {
        
    // }
    
    /** @test **/
    public function an_authenticated_investor_can_cancel_his_bid_on_an_asset()
    {
        $investor = create(Investor::class, ['wallet' => 20000, 'escrow' => 10000]);

        $bid = create(Bid::class, ['investor_id' => $investor->id, 'status' => 1, 'amount' => 5000]);
        
        $this->signIn($investor, 'investor');
        
        $this->post("bids/{$bid->id}/cancel")
            ->assertStatus(200);
        
        $this->assertEquals(4, $bid->fresh()->status);
        $this->assertEquals(5000, $investor->fresh()->escrow);
        $this->assertEquals(25000, $investor->fresh()->wallet);
    }
}
