<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettleAffiliateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Manual Affiliate Testing Works
     * @group Maintenance
     * @author Keania
     */
    public function testAdminCanSettleAffiliates()
    {
        $loan = factory(\App\Models\Loan::class)->create();

        $loanRequest = $loan->loanRequest;

        $loanRequest->update(['status'=>4]);

        $loan->update(['status'=>'1']);

        $affiliateSettings =  json_encode(
            [
            'loan_vissibility'=> 'view_all_loans', 
            'commission_method'=>'disbursement'
            ]
        );

        $affiliate = factory(\App\Models\Affiliate::class)->create(
            ['settings'=> $affiliateSettings]
        );

        $affiliate->update(['commission_rate'=>2]);

        $wallet = $affiliate->wallet;

        $transactions = $affiliate->transactions->where('code', '019')->count();

        $admin  = factory(\App\Models\Admin::class)->create();

        $route = route('admin.affiliates.settle.commission');

        $data = [
            'affiliateId'=>$affiliate->id,
            'loanRequestId'=>$loanRequest->id
        ];

        $response = $this->actingAs($admin, 'admin')->post($route, $data);


        $response->assertStatus(302);

        $loanRequest->refresh();

        $affiliate->refresh();

        $this->assertTrue($loanRequest->placer_id == $affiliate->id);

        $loan->refresh();

        $this->assertTrue($loan->collector_id == $affiliate->id);

        $commission = round($loanRequest->amount * ($affiliate->commission_rate/100), 2);

        $affiliate->refresh();

        $newwallet = round($commission + $wallet, 2);

        $this->assertTrue(round($affiliate->wallet, 2) == $newwallet);
    }
}
