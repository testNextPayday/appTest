<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\LoanRequest;
use App\Models\User;

class LoanRequestTest extends TestCase
{
    use RefreshDatabase;
    
    protected $loanRequest;

    public function setUp(): void
    {
        parent::setUp();
        $this->loanRequest = create(LoanRequest::class);    
    }
    
    /** @test **/
    public function a_loan_request_can_generate_its_own_reference()
    {
        $this->assertNotNull($this->loanRequest->reference);
    }
    
    /** @test **/
    public function a_loan_request_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->loanRequest->user);
    }
}
