<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Loan;

class LoanTest extends TestCase
{
    use RefreshDatabase;
    
    protected $loan;

    public function setUp(): void
    {
        parent::setUp();
        $this->loan = create(Loan::class);
    }
    
    /** @test **/
    public function a_loan_can_generate_its_own_reference()
    {
        $this->assertNotNull($this->loan->reference);
    }
    
    /** @test **/
    public function a_loan_can_update_the_status_of_its_collection_method()
    {
        $loan = create(Loan::class, [
            'collection_methods' => json_encode([
                ["code" => "100", "status" => 0, "type" => 'primary'],   
            ])
        ]);
        
        
        $loan->updateCollectionMethodStatus("100", 1);
        
        
        $this->assertEquals(
            $loan->fresh()->collection_methods,
            json_encode([["code" => "100", "status" => 1, "type" => 'primary']])
        );
    }
}
