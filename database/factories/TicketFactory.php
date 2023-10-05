<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\Ticket::class, function (Faker $faker) {
    return [
        //
        'owner_id'=> factory(\App\Models\User::class)->create()->id,
        'owner_type'=> 'App\Models\User',
        'reference'=> '#'.$faker->numberBetween($min = 1000, $max = 50000),
        'type' => $faker->randomElement(['Technical', 'General Support']),
        'priority'=> $faker->randomElement(['Low', 'Medium', 'High']),
        'subject'=> $faker->sentence
    ];
});
