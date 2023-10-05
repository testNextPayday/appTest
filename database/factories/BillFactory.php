<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(\App\Models\Bill::class, function (Faker $faker) {

    return [
        //

        'name'=> $faker->firstName.' Bill',

        'amount'=> $faker->numberBetween($min = 30000, $max=50000),

        'desc'=> ' This describes a utility bill'
    ];
});
