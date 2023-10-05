<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(\App\Models\Employment::class, function (Faker $faker) {

    return [
        //
        'user_id'=>factory(\App\Models\User::class)->create()->id,
        'employer_id'=>factory(\App\Models\Employer::class)->create()->id,
        'position'=>$faker->randomElement(['Clerk','Manager','GM']),
        'payroll_id'=>$faker->numberBetween($min = 12345, $max = 234567),
        'monthly_salary'=>$faker->numberBetween($min = 12345, $max = 234567)
    ];
});
