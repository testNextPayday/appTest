<?php

namespace Database\Factories;

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(\App\Models\PromissoryNote::class, function (Faker $faker) {

    $tenure = $faker->randomElement([3, 6, 9, 12]);
    $start_time =  Carbon::parse($faker->dateTime($max = 'now')->format('Y m D'));
    $mature_time = $start_time->addMonths($tenure);
    return [
        //
        'investor_id'=> factory(\App\Models\Investor::class)->create()->id,
        'interest_payment_cycle'=> $faker->randomElement(['monthly', 'backend', 'upfront']),
        'start_date'=>$start_time->toDateString(),
        'maturity_date'=>$mature_time->toDateString(),
        'principal'=> $faker->numberBetween($min=10000, $max=20000000000),
        'tenure'=> $tenure,
        'rate'=> $faker->randomElement([1, 2, 4, 6, 5]),
        'interest'=> $faker->numberBetween($min=1000, $max=10000),
        'tax'=> $faker->randomElement([2, 3, 4, 6]),
        'reference'=>'NPRN-'.time().rand(1, 7).$faker->randomElement(range(1, 100)),
    ];
});
