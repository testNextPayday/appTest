<?php

namespace Tests\Feature\Investor;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Investor;
use App\Models\WalletTransaction;

class ViewTransactionsTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test **/
    public function an_authenticated_investor_can_view_all_his_transactions()
    {
        $investor = create(Investor::class);
        $transactions = create(WalletTransaction::class, [
            'owner_id' => $investor->id,
            'owner_type' => get_class($investor)
        ], 2)->toArray();
        $otherTransaction = create(WalletTransaction::class);
        
        $this->signIn($investor, 'investor');
        
        $this->get('investor/transactions')
            ->assertSee(strtoupper($transactions[0]['reference']))
            ->assertSee(strtoupper($transactions[1]['reference']))
            ->assertDontSee($otherTransaction->reference);
        
    }
}
