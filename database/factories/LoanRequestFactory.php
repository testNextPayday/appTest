<?php

namespace Database\Factories;

use Faker\Generator as Faker;

use App\Models\User;

use App\Models\LoanRequest;

$factory->define(LoanRequest::class, function (Faker $faker) {
    $employment  = factory(\App\Models\Employment::class)->create();
    $user = factory(User::class)->create();
    
    return [
        'user_id' => $user->id,
        'employment_id'=> $employment->id,
        'amount' => 10000,
        'interest_percentage' => 5,
        'duration' => 6,
        'bank_statement' => '/path/to/file',
        'pay_slip' => '/path/to/file',
        'comment' => $faker->sentence,
        'acceptance_code' => $faker->word,
        'expected_withdrawal_date' => $faker->date,
        'acceptance_expiry' => $faker->date,
        'percentage_left' => 100,
        'placer_type'=>'App\Models\Staff',
        'mandateId' => '270007726086',
        'requestId' => '1547462543233',
        'reference'=>'LR-'.time().rand(1,7).$faker->randomElement(range(1, 100)),
        'upfront_interest' => 0
    ];
});
