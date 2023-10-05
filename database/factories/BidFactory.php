<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\Bid::class, function (Faker $faker) {
    return [
        'investor_id' => function() {
            return factory(App\Models\Investor::class)->create()->id;
        },
        
        'fund_id' => function() {
            return factory(App\Models\LoanFund::class)->create()->id;  
        },
        
        'amount' => 10000,
        'status' => 1
    ];
});
