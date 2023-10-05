<?php

namespace Tests\Unit\Traits;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Traits\Accounting;

class AccountingTest extends TestCase
{
    use Accounting;
    
    /** @test */
    public function it_returns_the_correct_principal()
    {
        $this->assertEquals(8.33, $this->getPrincipal(100, 12));   
    }
    
    /** @test */
    public function it_returns_the_correct_interest()
    {
        $rate = 5; $amount = 1000; $duration = 12;
        $this->assertEquals(5000.0, $this->getInterest($rate, $amount, $duration));
    }
    
}
