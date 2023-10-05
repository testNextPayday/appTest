<?php

namespace Tests\Unit\Investors;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoveFundToWalletTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group Maintenance
     * @author Keania
     * Test we can move funds to wallet
     *
     * @return void
     */
    public function testFundsMoveAfter48Hours()
    {
        $hoursAgo = Carbon::now()->subHours(48)->toDateTimeString();

        $testAmount = 30000;

        $investor = factory(\App\Models\Investor::class)->create(
            [
                'last_vault_inflow'=> $hoursAgo,
                'wallet'=> 0,
                'vault'=> $testAmount,
                'auto_invest'=> true
            ]
        );

        $this->assertTrue($investor->vault == $testAmount);

        $this->assertTrue($investor->wallet == 0);

        // call the run command
        Artisan::call('investors:move_funds');

        $investor->refresh();

        $this->assertTrue($investor->vault == 0);

        $this->assertTrue($investor->wallet == $testAmount);

        $this->assertTrue($investor->last_vault_inflow == null);

    }

    
    /**
     * Test that funds would not be swept before 48hrs
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testFundsDoesNotFundBefore48Hours()
    {
        $hoursAgo = Carbon::now()->subHours(45)->toDateTimeString();

        $testAmount = 30000;

        $investor = factory(\App\Models\Investor::class)->create(
            [
                'last_vault_inflow'=> $hoursAgo,
                'wallet'=> 0,
                'vault'=> $testAmount,
                'auto_invest'=> true
            ]
        );

        $this->assertTrue($investor->vault == $testAmount);

        $this->assertTrue($investor->wallet == 0);

        // call the run command
        Artisan::call('investors:move_funds');

        $investor->refresh();

        $this->assertFalse($investor->vault == 0);

        $this->assertFalse($investor->wallet == $testAmount);
    }
}
