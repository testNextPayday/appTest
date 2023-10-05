<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\WalletTransaction;
use App\Models\User;
use App\Models\Investor;

class WalletTransactionTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test **/
    public function a_wallet_transaction_has_an_owner()
    {
        $investor = create(Investor::class);
        $transaction = create(WalletTransaction::class, [
            'owner_id' => $investor->id,
            'owner_type' => get_class($investor)
        ]);
        
        $this->assertInstanceOf('App\Models\Investor', $transaction->owner);
        
        $borrower = create(User::class);
        
        $borrowerTransaction = create(WalletTransaction::class, [
            'owner_id' => $borrower->id,
            'owner_type' => get_class($borrower)
        ]);
        
        $this->assertInstanceOf('App\Models\User', $borrowerTransaction->owner);
    }
}
