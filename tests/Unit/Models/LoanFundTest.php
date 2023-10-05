<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\LoanFund;
use App\Models\LoanRequest;
use App\Models\Investor;

class LoanFundTest extends TestCase
{
    use RefreshDatabase;
    
    protected $loanFund;

    public function setUp(): void
    {
        parent::setUp();
        $this->loanFund = create(LoanFund::class);    
    }
    
    /** @test **/
    public function a_loan_fund_belongs_to_a_loan_request()
    {
        $this->assertInstanceOf(LoanRequest::class, $this->loanFund->loanRequest);    
    }
    
    /** @test **/
    public function a_loan_fund_belongs_to_an_investor()
    {
        $this->assertInstanceOf(Investor::class, $this->loanFund->investor);
    }
    
    /** @test **/
    public function a_loan_fund_can_generate_its_own_reference()
    {
        $this->assertNotNull($this->loanFund->reference);
    }
}
