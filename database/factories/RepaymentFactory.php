<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\Repayment::class, function (Faker $faker) {
    return [
        //
        'fund_id'=> factory(\App\Models\LoanFund::class),
        'loan_id'=> factory(\App\Models\Loan::class),
        'user_id'=> factory(\App\Models\User::class),
        'investor_id'=> factory(\App\Models\Investor::class),
        'plan_id'=> factory(\App\Models\RepaymentPlan::class),
        'amount'=> 1000,
        'commission'=> 500,
        'tax'=> 200
    ];
});
