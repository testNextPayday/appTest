<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettleAffiliateOnWalletFundTest extends TestCase
{
    /**
     * @group Maintenance
     * @author Keania
     * Setting affiliate works successfully
     *
     * @return void
     */
    public function testWeCanPayAffiliatesOnPreviousFundings()
    {
        $affiliate = factory(\App\Models\Affiliate::class)->create();

        $investor = factory(\App\Models\Investor::class)->create(
            ['adder_id'=> $affiliate->id, 'adder_type'=>get_class($affiliate)]
        );

        $admin = factory(\App\Models\Admin::class)->create();

        $this->fundAccount($investor, 300000);

        $data = [
            'receiverType'=> strtolower($affiliate->toHumanReadable()),
            'fund_id'=> $investor->transactions->where('code', '000')->first()->id,
            'assignedPersonId'=> $affiliate->id
        ];

        $route = route('admin.investors.pay-fund-commission');

        $response = $this->actingAs($admin, 'admin')->post($route, $data);


        $response->assertSessionHas('success', 'Successfully paid commission');

    }


      /**
       * @group Maintenance
     * @author Keania
     * Setting affiliate works successfully
     *
     * @return void
     */
    public function testWeCanPayStaffsOnPreviousFundings()
    {
        $affiliate = factory(\App\Models\Staff::class)->create();

        $investor = factory(\App\Models\Investor::class)->create(
            ['adder_id'=> $affiliate->id, 'adder_type'=>get_class($affiliate)]
        );

        $admin = factory(\App\Models\Admin::class)->create();

        $this->fundAccount($investor, 300000);

        $data = [
            'receiverType'=> strtolower($affiliate->toHumanReadable()),
            'fund_id'=> $investor->transactions->where('code', '000')->first()->id,
            'assignedPersonId'=> $affiliate->id
        ];

        $route = route('admin.investors.pay-fund-commission');

        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $response->assertSessionHas('success', 'Successfully paid commission');

    }


    protected function  fundAccount($investor, $amount)
    {
        $financeHandler = new FinanceHandler(new TransactionLogger);
        $code = config('unicredit.flow')['wallet_fund'];
        $financeHandler->handleSingle(
            $investor, 'credit', $amount, null, 'W', $code
        );

    }
}
