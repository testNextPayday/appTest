<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvestorLoanMigrationTest extends TestCase
{
    /**
     * @group Maintenance
     * @author Keania
     *
     * @return void
     */
    public function testInvestorLoanMigration()
    {
        $loanFund = factory(\App\Models\LoanFund::class)->create();

        $admin = factory(\App\Models\Admin::class)->create();

        $owningInvestor = $loanFund->investor;

        $buyingInvestor = factory(\App\Models\Investor::class)->create(
            ['wallet'=>$loanFund->amount * 2]
        );

        $buyerWallet = $buyingInvestor->wallet;

        $sellerWallet = $owningInvestor->wallet;

        $selectedLoanFund = [$loanFund];

        $currentValue = $loanFund->currentValue;

        $data = [
            'to'=>json_encode($buyingInvestor),
            'from'=>json_encode($owningInvestor),
            'loanFundCurrentValue'=>json_encode($currentValue),
            'selectedLoanFund'=> json_encode($selectedLoanFund)
        ];
        $route = route('admin.loanfund.migration');

        $response = $this->actingAs($admin, 'admin')->json('POST', $route, $data);

        $loanFund->refresh();

        $this->assertTrue($loanFund->status == 5);

        $this->assertTrue($loanFund->sale_amount == $currentValue);

        $buyingInvestor->refresh();

        $owningInvestor->refresh();

        $this->assertTrue($buyingInvestor->wallet == ($buyerWallet - $currentValue));

        $this->assertTrue($owningInvestor->wallet == ($sellerWallet + $currentValue));
    }
}
