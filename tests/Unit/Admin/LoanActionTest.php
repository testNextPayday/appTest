<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanActionTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test admin can toggle card sweep pause
     *
     * @return void
     */
    public function testToggleSweepWorks()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $loan = factory(\App\Models\Loan::class)->create(['collection_plan'=> 300]);

        $billing = factory(\App\Models\BillingCard::class)->create(['user_id'=> $loan->user->id]);

        $this->signIn($admin, 'admin');

        $route  = route('admin.loans.pause-sweep-toggle', ['reference'=> $loan->reference]);

        $response = $this->get($route);

        $this->assertTrue($loan->pause_sweep == !$loan->fresh()->pause_sweep);
    }
}
