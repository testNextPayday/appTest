<?php

namespace Tests\Feature\Dev;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\Dev\BackendWithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BackendWithdrawalServiceTest extends TestCase
{
    /**
     * Test withdrawal works
     *
     * @return void
     */
    public function testWithdrawalWorks()
    {
        $investor = factory(\App\Models\Investor::class)->create(
            ['wallet'=> 2000000]
        );

        $oldWallet = $investor->wallet;

        $amount = 1000000;

        $backendService = new BackendWithdrawalService();

        $backendService->init($investor, $amount);

        $investor->refresh();

        $this->assertTrue($investor->escrow == $amount);

        $backendService->end();

        $this->assertTrue($investor->escrow == 0);

        $this->assertTrue($investor->wallet == $oldWallet - $amount);

        
    }
}
