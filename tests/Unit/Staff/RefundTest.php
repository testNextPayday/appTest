<?php

namespace Tests\Unit\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Staff;
use Tests\Unit\Traits\RefundTest as RefundTrait;

class RefundTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */

    use RefreshDatabase;
     
    use RefundTrait;

     /**
     * A basic unit test example.
     *  @author Alisi
     *  @group Maintenance
     * @return void
     */
    public function test_staff_can_make_refund()
    {
        
        return $this->canMakeRefund(route('staff.create.refund'),Staff::class,'staff');
    }
}
