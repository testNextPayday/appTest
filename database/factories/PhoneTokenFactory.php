<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\PhoneToken::class, function (Faker $faker) {
    return [
        //
        'token'=>'NPD-'.rand(1, 7),
        'phone'=>$faker->phoneNumber
    ];
});
