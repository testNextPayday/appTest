<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\LoanFund;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanFundFulfillmentTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Get the prerequisites
     *
     * @return void
     */
    protected function getPreRequisites() 
    {
        $loanRequest = factory(\App\Models\LoanRequest::class)->create();
        
        $loanFund = factory(\App\Models\LoanFund::class)->create(['request_id'=>$loanRequest->id, 'status'=> '2']);

        $loan = factory(\App\Models\Loan::class)->create(['request_id'=> $loanRequest->id]);
       
        return [$loanRequest, $loanFund, $loan];
    }


    /**
     * Test a loan fund with fulfilled loan gets fulfilled 
     * @group Maintenance
     * 
     * @author Keania
     * @return void
     */
    public function testWeCanFulfillALoanFund()
    {
        
        list($loanRequest, $loanFund, $loan) = $this->getPreRequisites();
    
        $loan->update(['status'=>'2']);

        Artisan::call('fulfill:funds');

        $loanFund->refresh();
       
        $this->assertTrue($loanFund->status == '6');
    }


    /**
     * Test an active loan's loan fund cannot be pushed fulfilled
     * @group Maintenance
     * 
     * @author Keania
     * @return void
     */
    public function testWeCannotFulfillALoanFund()
    {
        list($loanRequest, $loanFund, $loan) = $this->getPreRequisites();

        $loan->update(['status'=>'1']);

        Artisan::call('fulfill:funds');

        $loanFund->refresh();

        $this->assertTrue($loanFund->status == '2');
    }
}
