<?php

namespace Database\Factories;

use App\Models\Investor;

use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Investor::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
        'avatar' => 'public/defaults/avatars/default.png',
        'wallet' => 0.00,
        'escrow' => 0.00,
        'is_verified' => true,
        'wallet'=>$faker->numberBetween($min=30000, $max=50000),
        'auto_invest'=>false,
        'loan_fraction'=>$faker->randomElement([25,50,100]),
        'employer_loan'=>json_encode([1]),
        'loan_tenors'=>6,
        'is_active'=>1,
        'payback_cycle'=>'backend',
        'reference'=>'UC-IN-'.time().rand(1, 7).$faker->randomElement(range(1, 100)),
        'source_of_income'=> 'Yahoo and Embezzlement'
    ];
});
