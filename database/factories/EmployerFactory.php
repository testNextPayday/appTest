<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\Employer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
        'address' => $faker->address,
        'state' => $faker->state,
        'payment_date' => '10/12',
        'is_verified' => true,
        'is_primary' => $faker->randomElement([1, 0]),
        'payment_mode' => 1,
        'employee_count' => 10,
        'approver_name' => $faker->name,
        'approver_designation' => $faker->word,
        'approver_phone' => $faker->phoneNumber,
        'approver_email' => $faker->email,
        'rate_3' => 10,
        'rate_6' => 7,
        'rate_12' => 5,
        'fees_3' => 1500,
        'fees_6' => 1200,
        'fees_12' => 1000,
        'max_tenure' => 12,
        'collection_plan' => 100,
        'user_id' => null,
        'collection_plan_secondary' => 200,
        'upgrade_enabled' => 0,
        'upfront_interest' => 0
    ];
});
