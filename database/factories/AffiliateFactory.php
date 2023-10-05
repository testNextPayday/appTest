<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(App\Models\Affiliate::class, function (Faker $faker) {
    return [
        //
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'reference'=>'NPU-'.time().rand(1,7).$faker->randomElement(range(1,200)),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
        'avatar' => 'public/defaults/avatars/default.png',
        'wallet' => 0.00,
        'escrow' => 0.00,
        'status' => true
    ];
});
