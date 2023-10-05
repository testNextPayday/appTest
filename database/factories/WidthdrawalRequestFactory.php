<?php

namespace Database\Factories;

use Faker\Generator as Faker;

use App\Models\WithdrawalRequest;
use App\Models\Investor;

$factory->define(WithdrawalRequest::class, function (Faker $faker) {
    return [
        'requester_id' => function() {
            return factory(Investor::class)->create()->id;
        },
        'requester_type' => 'App\Models\Investor',
        'reference' => $faker->word,
        'amount' => 10000,
        'status' => 1
    ];
});
