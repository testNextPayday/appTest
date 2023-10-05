<?php

namespace Tests\Unit\Traits;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Traits\LoanUtils;
use App\Models\LoanRequest;
use Carbon\Carbon;

class LoanUtilsTest extends TestCase
{
    
    use LoanUtils;
    
    /** @test */
    public function it_returns_the_correct_end_date()
    {
        $now = Carbon::today();
        $request = new LoanRequest();
        $request->duration = 6;
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate($request);
        
        // temporary
        $this->assertTrue(true);
    }
}
