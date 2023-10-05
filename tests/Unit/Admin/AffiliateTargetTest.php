<?php

namespace Tests\Unit\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AffiliateTargetTest extends TestCase
{   

    use RefreshDatabase;
    
    /**
     * Sets up the test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
    }

    
    /**
     * @group Maintenance
     * 
     * @author Keania
     * Admin can create target
     *
     * @return void
     */
    public function testAffiliateCanBeAssignedTarget()
    {
        $affiliates = factory(\App\Models\Affiliate::class, 3)->create();

        $admin = factory(\App\Models\Admin::class)->create();

        $route = route('admin.affiliate.target-store');

        $data = array(
            'name'=> $this->faker->firstName. ' Target',
            'category'=> 'selective',
            'reward'=> 3000,
            'days'=> 30,
            'type'=> 'book_loans',
            'target'=> $this->faker->numberBetween($min = 5000, $max = 100000),
            'affiliates'=> $affiliates->toArray()
        );

        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $this->assertDatabaseHas(
            'targets', 
            [
                'name'=> $data['name'], 
                'category'=>$data['category'], 
                'reward'=> $data['reward']
            ]
        );

        $this->assertDatabaseHas(
            'targettables',
            [
                'targettable_id'=> $affiliates->first()->id,
            
            ]
        );
    }

    
    /**
     * All affiliates get assigned all targets
     * @group Maintenance
     * 
     * @author Keania
     * @return void
     */
    public function testAllTargetsGetAssignedToAllAffiliates()
    {
        $allAffiliates = factory(\App\Models\Affiliate::class, 3)->create();

        $admin = factory(\App\Models\Admin::class)->create();

        $route = route('admin.affiliate.target-store');

        $data = array(
            'name'=> $this->faker->firstName. ' Target',
            'category'=> 'all',
            'reward'=> 3000,
            'days'=> 30,
            'target'=> $this->faker->numberBetween($min = 5000, $max = 100000),
            'type'=> 'book_loans'
        );

        $response = $this->actingAs($admin, 'admin')->post($route, $data);

        $this->assertDatabaseHas(
            'targets', 
            [
                'name'=> $data['name'], 
                'category'=>$data['category'], 
                'reward'=> $data['reward']
            ]
        );

        foreach ($allAffiliates as $affiliate) {

            $this->assertDatabaseHas(
                'targettables',
                [
                    'targettable_id'=> $affiliate->id,
                
                ]
            );
        }

        
    }
}
