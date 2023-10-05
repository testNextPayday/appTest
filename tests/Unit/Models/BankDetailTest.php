<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\BankDetail;
use App\Models\User;

class BankDetailTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test **/
    public function a_bank_detail_has_an_owner()
    {
        $bank = create(BankDetail::class);
        
        $this->assertInstanceOf('App\Models\Investor', $bank->owner);
        
        $borrower = create(User::class);
        
        $borrowerBank = create(BankDetail::class, [
            'owner_id' => $borrower->id,
            'owner_type' => User::class
        ]);
        
        $this->assertInstanceOf('App\Models\User', $borrowerBank->owner);
    }
}
