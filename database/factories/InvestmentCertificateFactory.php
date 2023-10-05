<?php

namespace Database\Factories;

use Faker\Generator as Faker;
use App\Models\InvestmentCertificate;

$factory->define(InvestmentCertificate::class, function (Faker $faker) {
    return [
        //
        'name'=> $faker->name,
        'start_date'=> $faker->dateTime($max = 'now')->format('Y-m-d H:i:s'),
        'amount'=> $faker->numberBetween($min = 1000, $max = 9000),
        'interest_payment_cycle'=> $faker->randomElement(['upfront', 'backend']),
        'tenure'=> $faker->randomElement([3, 6, 9, 12]),
        'rate'=> $faker->randomElement([4,6,7,3])
    ];
});
