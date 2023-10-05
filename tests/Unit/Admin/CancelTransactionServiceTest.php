<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\WalletTransaction\CancelTransactionService;

class CancelTransactionServiceTest extends TestCase
{

    use RefreshDatabase;
    
    /**
     * SetUp test 
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        
        $financeHandler = new FinanceHandler(new TransactionLogger);

        $this->cancelService = new CancelTransactionService($financeHandler);
    }


    /**
     * Test a code not in whitelist throws an error
     *
     * @expectedException DomainException
     */
    public function testWeCannotCancelANonWhitelistedTransaction()
    {
        $transaction = factory(\App\Models\WalletTransaction::class)->create();

        $this->cancelService->cancelTransaction($transaction);
    }

    
    /**
     * Teat we can cancell a whitelisted transaction
     *
     * @return void
     */
    public function testWeCanCancelAWhitelistedTransaction()
    {
        $transaction = factory(\App\Models\WalletTransaction::class)->create(
            ["code"=> '024']
        );

        $investor = $transaction->owner;

        $amount = $transaction;

        $this->cancelService->cancelTransaction($transaction);

        $transaction->refresh();

        $this->assertEquals($transaction->cancelled, 1);

        // Assert the transaction now owns a transaction in the database
        $this->assertDatabaseHas(
            'wallet_transactions', [
            'owner_id'=> $investor->id,
            'owner_type'=> get_class($investor),
            'entity_id'=> $transaction->id,
            'entity_type'=> get_class($transaction),
            'amount'=> $transaction->amount
            ]
        );

        // Asert owner balance has been increased or decreased 

        
    }
}
