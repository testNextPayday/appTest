<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportStatisticsTest extends TestCase
{

    protected $reportList = [
        'loan-disbursed',
        'collections-made',
        'fees-earned',
        'commissions-given',
        'active-loans',
        'penalties',
        'insurances',
        'investments',
        'repayments',
       
    ];
    /**
     * A basic unit test example.
     *
     * @author Keania
     * @group Maintenance
     */
    public function testReportStatsLoanDisbursed()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'loans-disbursed']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }


    /**
     * A basic unit test example.
     *
     *  @author Keania
     * @group Maintenance
     */
    public function testReportCollectionMade()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'collections-made']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }



    /**
     * A basic unit test example.
     *
     *  @author Keania
     * @group Maintenance
     */
    public function testReportStatsFeesEarned()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'fees-earned']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }


    /**
     * A basic unit test example.
     *
     *  @author Keania
     * @group Maintenance
     */
    public function testReportCommissionsGiven()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'commissions-given']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }

    /**
     * A basic unit test example.
     * @author Keania
     * @group Maintenance
     * @return void
     */
    public function testReportActiveLoans()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'active-loans']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }


    /**
     * A basic unit test example.
     * @author Keania
     * @group Maintenance
     * @return void
     */
    public function testReportPenalties()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'penalties']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }

    /**
     * A basic unit test example.
     * @author Keania
     * @group Maintenance
     * @return void
     */
    public function testReportInsurance()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'insurances']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }


    /**
     * A basic unit test example.
     * @author Keania
     * @group Maintenance
     * @return void
     */
    public function testReportInvestments()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'investments']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }

    /**
     * A basic unit test example.
     * @ author Keania
     * @ group Maintenance
     * @ return void
     */
    public function testReportRepayments()
    {
        $admin  = factory(\App\Models\Admin::class)->create();

        $url = route('admin.report.statistics', ['name'=>'repayments']);

        $response = $this->actingAs($admin, 'admin')->get($url);

        $response->assertStatus(200);
        
    }


   
}
