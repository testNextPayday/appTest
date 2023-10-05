<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\Unit\Traits\RefundTest as RefundTrait;
use App\Models\Admin;

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
    public function test_admin_can_make_refund()
    {
        return $this->canMakeRefund(route('admin.create.refund'),Admin::class,'admin');
    }
}
