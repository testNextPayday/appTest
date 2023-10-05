<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AffiliateSettingsTest extends TestCase
{
    /**
     * @group Maintenance
     * @author Keania
     * 
     * Admin can update settings
     *
     * @return void
     */
    public function testAdminCanMakeSettings()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $affiliate = factory(\App\Models\Affiliate::class)->create();

        $route = route(
            'admin.affiliate.settings', 
            ['affiliate'=>$affiliate->reference]
        );

        $data = [
            'loan_vissibility'=> 'view_all_loans', 
            'commission_method'=>'repayment'
        ];

        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $response->assertSessionHas('success');

        $affiliate->refresh();

        $this->assertEquals(json_encode($data), $affiliate->settings);


    }
}
