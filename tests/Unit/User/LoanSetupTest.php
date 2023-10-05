<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanSetupTest extends TestCase
{
    /**
     * A user can access loan setup page
     * 
     *
     * @return void
     */
    public function testCanViewSetup()
    {
        $user = factory(\App\Models\User::class)->create();

        $loan  = factory(\App\Models\Loan::class)->create();

        $route = route('users.loan.setup.dashboard', ['loan'=>$loan]);
        
        $response = $this->actingAs($user)->get($route);

        $response->assertStatus(200);
    }
}
