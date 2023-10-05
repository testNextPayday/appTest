<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(

    \App\Models\Settlement::class, function (Faker $faker) {

        $loan =  factory(\App\Models\Loan::class)->create();

        $plans = factory(\App\Models\RepaymentPlan::class, 3)->create(
            ['loan_id'=>$loan->id]
        );

    return [
        
        'loan_id'=> $loan->id,
        'amount'=> $faker->numberBetween($min = 1000, $max = 3000),
        'status'=> 1,
        'collection_method'=> 'Test',
        'investors_cut'=> $faker->numberBetween($min = 1000, $max = 3000),
         ];
}
);
