<?php

namespace Tests\Feature\System;

use Tests\TestCase;
use App\Services\TargetService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TargetPaymentTest extends TestCase
{

    use RefreshDatabase;

    protected $faker;

    protected $targetService;

     /**
     * Sets up the test
     * @group Maintenance
     * @author Keania
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->targetService = new TargetService();
    }

 


    /**
     * Test an affiliate fulfils target is paid
     *
     * @return void
     */
    public function testAffiliateFulfilsTargetIsPaid()
    {

        // STEP 1 : create affiliate
        $affiliates = factory(\App\Models\Affiliate::class, 3)->create();

        $admin = factory(\App\Models\Admin::class)->create();

        $route = route('admin.affiliate.target-store');

        // STEP 2 : create target
        $data = array(
            'name'=> $this->faker->firstName. ' Target',
            'category'=> 'selective',
            'reward'=> 3000,
            'days'=> 30,
            'type'=> 'book_loans',
            'target'=> $this->faker->numberBetween($min = 5000, $max = 100000),
            'affiliates'=> $affiliates->toArray()
        );

        $this->actingAs($admin, 'admin')->post($route, $data);

        // STEP 3 : book loan worth that amount
        $firstAffiliate = $affiliates->first();
        $oldWallet = $firstAffiliate->wallet;

        $loan = factory(\App\Models\Loan::class)->create(
            [
                'collector_id'=>$firstAffiliate->id,
                'collector_type'=> 'App\Models\Affiliate',
                'amount'=> $data['target']
            ]
        );

        // STEP 4 : Backdate the target to have expired yestderday
        $today = \Carbon\Carbon::now();
        $yesterday = $today->copy()->subDays(1)->toDateString();

        $target = $firstAffiliate->targets->last();
        $target->update(
            ['expires_at'=> $yesterday]
        );

        // STEP 5: Call the artisan check target
        \Artisan::call('target:check');
        
        //STEP 6: assert that the first user met the target
        $this->assertTrue($this->targetService->metTarget($firstAffiliate, $target));

        //STEP 7: assert thet the second user did not met the target
        $this->assertFalse($this->targetService->metTarget($affiliates->last(), $target));

        //STEP 8:  assert the wallet has increased by amount of target reward
        $expectedWallet = $oldWallet + $target->reward;
        $this->assertEquals($firstAffiliate->fresh()->wallet, $expectedWallet);
    }
}
