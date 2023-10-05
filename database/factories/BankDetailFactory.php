<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\BankDetail::class, function (Faker $faker) {
    return [
        'owner_id' => function () {
            return factory(App\Models\Investor::class)->create()->id;
        },
        'owner_type' => 'App\Models\Investor',
        'bank_name' => $faker->word,
        'account_number' => 1236547895,
        'bank_code' => 057
    ];
});
