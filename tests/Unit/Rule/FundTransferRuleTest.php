<?php

namespace Tests\Unit\Rule;

use Tests\TestCase;
use App\Rules\Investor\FundTransferRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FundTransferRuleTest extends TestCase
{

    protected $rule;


    public function setUp(): void
    {
        parent::setUp();
        $this->rule =  new FundTransferRule();
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testHasValuePass($value)
    {
        $this->assertTrue($this->rule->passes('test',$value));
    }

    public function testHasNoValuePass($value)
    {
        $this->assertFalse($this->rule->passes('test',$value));
    }


    
}
