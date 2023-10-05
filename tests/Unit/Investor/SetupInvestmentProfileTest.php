<?php

namespace Tests\Unit\Investor;

use Tests\TestCase;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SetupInvestmentProfileTest extends TestCase
{
    /**
     * @group Maintenance
     *  @author Keania
     * 
     */
    public function testInvestorCanSetUpInvestmentProfile()
    {
      
        $profileData = [
            'auto_rollover'=>true,
            'auto_invest'=>true,
            'loan_fraction'=>50,
            'employer_loan'=>factory(\App\Models\Employer::class)->create()->id,
            'loan_tenors'=>6,
            'payback_cycle'=>'Quaterly'
        ];

        $investor = factory(\App\Models\Investor::class)->create();
        $response = $this->be($investor,'investor')->post(route('investor.setup.investment.profile'),$profileData);
        $response->assertStatus(200);
        $response->assertJson(['success'=>'Profile was successfully saved']);
        $this->assertDatabaseHas('investors',$profileData);

    }
}
