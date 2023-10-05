<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteLoanWalletTransaction extends TestCase
{
    /**
     * Test admin can delete wallet transaction
     *
     * @return void
     */
    public function testWalletTransactionsCanBeDeleted()
    {
        $admin = factory(\App\Models\Admin::class)->create();

        $trnx = factory(\App\Models\LoanWalletTransaction::class)->create();

        
    }
}
