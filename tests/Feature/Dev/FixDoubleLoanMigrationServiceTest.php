<?php

namespace Tests\Feature\Dev;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Dev\FixDoubleLoanMigrationService;

class FixDoubleLoanMigrationServiceTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->fixingService = new FixDoubleLoanMigrationService();
    }

    /**
     * Test we safely reverse double migrations for a loan fund
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function testDoubleMigrateReverse()
    {
        
        list($loanFund, $childFund1, $childFund2) = $this->createFundAndChildAssets();
    
        $this->checkChildAssetIs($loanFund, 2); // Check child asset is 2

        $lastSold = $this->fixingService->getLastSoldChild($loanFund);
        
        //ensure child 3 is the last sold
        $this->assertEquals($childFund2->fresh()->toArray(), $lastSold->toArray());

        // reverse 
        $this->fixingService->reverseDoubleMigrate($loanFund);

        $loanFund->refresh();

        $this->checkChildAssetIs($loanFund, 1); // Check child asset is now 1

        $this->checkExistsRightBalances($loanFund, $childFund1, $childFund2);

    }

    
    /**
     * Creates Child fund and assets
     *
     * @return void
     */
    private function createFundAndChildAssets()
    {
        $loanFund = factory(\App\Models\LoanFund::class)->create();

        $childFund1 = $this->createChildFund($loanFund);
        
        $childFund2 = $this->createChildFund($loanFund);

        $this->setBalancesToZero($loanFund, $childFund1, $childFund2);
        
        $loanFund->update(['status'=>5, 'sale_amount'=> $loanFund->currentValue, 'transfer_date'=> now()]);

        return [$loanFund, $childFund1, $childFund2];
    }

    
    /**
     * Set the balance of the investors to zero 
     * This enables us to eknow what balancees entered
     * 
     * @param  mixed $loanFund
     * @param  mixed $childFund1
     * @param  mixed $childFund2
     * @return void
     */
    private function setBalancesToZero($loanFund, $childFund1, $childFund2)
    {
        $loanFund->investor->update(['wallet'=> 0]);
        $childFund1->investor->update(['wallet'=> 0]);
        $childFund2->investor->update(['wallet'=> 0]);
    }

    
    /**
     * Checks the child asset number while we reverse
     *
     * @param  mixed $loanFund
     * @param  mixed $number
     * @return void
     */
    private function checkChildAssetIs($loanFund, $number) 
    {
        $initialCount = $this->fixingService->getFundSoldCount($loanFund);

        // sold count is 2
        $this->assertTrue($initialCount == $number);
    }


    
    /**
     * Createsa child asset from a loanfund
     *
     * @param  mixed $fund
     * @return void
     */
    private function createChildFund($fund)
    {
        $data = [
            'original_id'=> $fund->id,
            'amount'=> $fund->currentValue,
            'status'=> 2
        ];

        $childLoan = factory(\App\Models\LoanFund::class)->create($data);

        return $childLoan;
    }

    
    /**
     * Checks exists right balances
     *
     * @param  mixed $loanFund
     * @param  mixed $childFund1
     * @param  mixed $childFund2
     * @return void
     */
    private function checkExistsRightBalances($loanFund, $childFund1, $childFund2)
    {
        $loanFund->refresh(); $childFund2->refresh();
        // the original loan fund becomes the seller fund
        $seller = $loanFund->investor;
        
        $buyer = $childFund2->investor;

        // seller has been debited
        $this->assertTrue($seller->wallet == -$childFund2->amount);

        // buyer has been credited
        $this->assertTrue($buyer->wallet == $childFund2->amount);
    }
}
