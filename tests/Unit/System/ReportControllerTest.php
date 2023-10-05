<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     * @author Keania
     * @group Maintenance
     * @return void
     */
    public function testLoanDisbursedAllEmployers()
    {
        $admin = factory(\App\Models\Admin::class)->create();
        $loan = factory(\App\Models\Loan::class)->create();
        $data = [
            'name'=>'disbursalReport',
            'from'=> now()->subDays(1)->toDateString(),
            'to'=> now()->addDays(1)->toDateString(),
            'info'=>'',
            'employer'=>null,
            'code'=>'001',
        ];

        $response = $this->json('POST', route('admin.report.fetch'), $data);

        $response->assertStatus(200);

    }

    /**
     * A basic unit test example.
     * @author Keania
     * @group Maintenance
     * @return void
     */
    public function testLoanDisbursedOneEmployer()
    {
        $admin = factory(\App\Models\Admin::class)->create();
        $loan = factory(\App\Models\Loan::class)->create();
        $data = [
            'name'=>'disbursalReport',
            'from'=> now()->subDays(1)->toDateString(),
            'to'=> now()->addDays(1)->toDateString(),
            'info'=>'',
            'employer'=>$loan->loanRequest->employment->employer_id,
            'code'=>'001',
        ];

        $response = $this->json('POST', route('admin.report.fetch'), $data);

        $response->assertStatus(200);

    }
}
