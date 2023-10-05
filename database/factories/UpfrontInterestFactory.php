<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(Model::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->define(App\Models\UpfrontInterest::class, function (Faker $faker) {
    return [
        'request_id' => $faker->request_id,
            'user_id' => $faker->user_id,
            'interest'=> $faker->interest,
            'mgt_fee' => $faker->mgt_fee,
            'loan_fee' => $faker->loan_fee,
            'total_payment' => $faker->total_payment
    ];
});

