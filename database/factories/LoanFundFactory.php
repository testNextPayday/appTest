<?php

namespace Database\Factories;

use Faker\Generator as Faker;

use App\Models\Investor;
use App\Models\LoanRequest;
use App\Models\LoanFund;

$factory->define(LoanFund::class, function (Faker $faker) {
    $investor = factory(Investor::class)->create();
    $loanRequest = factory(LoanRequest::class)->create();
    return [
        'investor_id' => $investor->id,
        'request_id' => $loanRequest->id,
        'percentage' => 5,
        'amount' => 10000,
        'reference'=>'ULF-'.time().rand(1, 7).$faker->randomElement(range(1, 100)),
    ];
});
