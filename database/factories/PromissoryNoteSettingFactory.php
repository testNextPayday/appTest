<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(\App\Models\PromissoryNoteSetting::class, function (Faker $faker) {
    return [
        //
        'name'=> $faker->randomElement(['monthly', 'backend', 'upfront']). ' Settings',
        'interest_rate'=> $faker->randomElement([3, 4, 5, 8]),
        'tax_rate' => $faker->randomElement([3, 4, 2])
    ];
});
