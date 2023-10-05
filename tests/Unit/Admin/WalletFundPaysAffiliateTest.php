<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use App\Models\Settings;
use Illuminate\Support\Facades\Event;
use App\Traits\ReferenceNumberGenerator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Events\Investor\InvestorWalletFundEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletFundPaysAffiliateTest extends TestCase
{
    use RefreshDatabase;

    use ReferenceNumberGenerator;

    public $refPrefix = 'TR-';

    /**
     * @group Maintenance
     * @author Keania
     * Fund Event Fires Affiliate Payment
     *
     * @return void
     */
    public function testAdminFundsInvestorAcctPaysAffiliate()
    {

        $affiliate = factory(\App\Models\Affiliate::class)->create(
            ['commission_rate_investor'=>3,]
        );

        $affWallet  = $affiliate->wallet;
        
        $admin  = factory(\App\Models\Admin::class)->create();

        $investor = factory(\App\Models\Investor::class)->create([
            'adder_id'=> $affiliate->id,
            'adder_type'=> get_class($affiliate)
        ]);

        $data = [
            'reference'=> $investor->reference,
            'code'=> $this->generateKey(),
            'amount'=> 250000
        ];

        $route = route('admin.users.fund-wallet');
        Notification::fake();
        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        // assert the affiliate was paid
        $commission = ($affiliate->commission_rate_investor / 100) * $data['amount'];

        $affiliate->refresh();

        $newBalance = $affWallet + $commission;

        $this->assertTrue($newBalance == $affiliate->wallet);

    }


     /**
      * @group Maintenance
     * @author Keania
     * Fund Event Fires Affiliate Payment
     *
     * @return void
     */
    public function testAdminFundsInvestorAcctPaysStaff()
    {

        Settings::create(['name'=>'Investor Funding Commission Rate', 'slug'=> 'investor_funding_commission_rate', 'value'=>3]);

        $affiliate = factory(\App\Models\Investor::class)->create();

        $affWallet  = $affiliate->wallet;
        
        $admin  = factory(\App\Models\Admin::class)->create();

        $investor = factory(\App\Models\Investor::class)->create([
            'adder_id'=> $affiliate->id,
            'adder_type'=> get_class($affiliate)
        ]);

        $data = [
            'reference'=> $investor->reference,
            'code'=> $this->generateKey(),
            'amount'=> 250000
        ];

        $route = route('admin.users.fund-wallet');
        Notification::fake();
        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        // assert the affiliate was paid
        $commission = (Settings::InvestorFundingCommissionRate() / 100) * $data['amount'];

        $affiliate->refresh();

        $newBalance = $affWallet + $commission;

        $this->assertTrue($newBalance == $affiliate->wallet);

    }
}
