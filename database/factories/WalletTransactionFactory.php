<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\WalletTransaction::class, function (Faker $faker) {
    return [
        'owner_id' => function() {
            return factory(App\Models\Investor::class)->create()->id;
        },
        'owner_type' => 'App\Models\Investor',
        'amount' => 2,
        'reference' => $faker->word,
        'code' => $faker->word,
        'description' => $faker->sentence
    ];
});
