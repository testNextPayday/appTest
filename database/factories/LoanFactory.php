<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\Loan::class, function (Faker $faker) {

    $user = factory(App\Models\User::class)->create();

    $loanRequest = factory(App\Models\LoanRequest::class)->create([
        'amount' => 100000,
        'status' => 4,
        'user_id' => $user->id
    ]);

    $loanFund = factory(App\Models\LoanFund::class)->create([
        'amount' => 20000,
        'request_id' => $loanRequest->id,
        'status' => 2
    ]);

    return [
        'request_id' => $loanRequest->id,
        'user_id' => $user->id,
        'amount' => 100000,
        'interest_percentage' => 5,
        'due_date' => $faker->datetime,
        'mandateId' => '270007726086',
        'requestId' => '1547462543233',
        'duration'=>3,
        'reference'=>'ULN-'.time().rand(1, 7).$faker->randomElement(range(1, 100)),
        'is_managed'=> 0
        
    ];
});
