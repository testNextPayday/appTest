<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\LoanRequest;

class PreparesLoanForDisbursementTest extends TestCase
{
    use RefreshDatabase;
    
    private $borrower;

    public function setUp(): void
    {
        parent::setUp();
        $this->borrower = create('App\Models\User');
        create('App\Models\BankDetail', [
            'owner_id' => $this->borrower->id,
            'owner_type' => 'App\Models\User'
        ]);
    }
    
    /** @test */
    public function an_admin_can_prepare_a_loan_from_a_loan_request()
    {
        $loanRequest = create(LoanRequest::class, [
            'percentage_left' => 0, 
            'user_id' => $this->borrower->id
        ]);
        
        $this->signIn(null, 'admin');
        
        $data = [
            'collection_plan' => 0,
            'sweep_start_day' => 10,
            'sweep_end_day' => 20,
            'sweep_frequency' => 3,
            'peak_start_day' => 5,
            'peak_end_day' => 6,
            'peak_frequency' => 24,
            'emi' => 2000  
        ];
        
        $this->post("/ucnull/loan-requests/prepare/{$loanRequest->reference}", $data);
        
        $this->assertDatabaseHas('loans', [
            'request_id' => $loanRequest->id,
            'status' => 0,
            // 'disburse_status' => 1
        ]);
    }
    
    /** @test */
    public function an_admin_can_prepare_a_das_loan_from_a_loan_request()
    {
        $this->borrower->generateRemitaAuthCode();
        $loanRequest = create(LoanRequest::class, [
            'percentage_left' => 0,
            'user_id' => $this->borrower->id
        ]);
        
        $this->signIn(null, 'admin');
        
        $data = [
            'collection_plan' => 1,
            'sweep_start_day' => 10,
            'sweep_end_day' => 20,
            'sweep_frequency' => 3,
            'peak_start_day' => 5,
            'peak_end_day' => 6,
            'peak_frequency' => 24,
            'emi' => 2000  
        ];
        
        $this->post("/ucnull/loan-requests/prepare/{$loanRequest->reference}", $data);
        
        $this->assertDatabaseHas('loans', [
            'request_id' => $loanRequest->id,
            'status' => 0,
            // 'disburse_status' => 2
        ]);
    }
}
