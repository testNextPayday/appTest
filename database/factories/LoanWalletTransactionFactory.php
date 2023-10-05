<?php

namespace Database\Factories;

use Faker\Generator as Faker;
use App\Models\LoanWalletTransaction;

$factory->define(LoanWalletTransaction::class, function (Faker $faker) {
    $plan = factory(\App\Models\RepaymentPlan::class)->create();
    $loan = $plan->loan;
    return [
        //
        'user_id'=> $loan->user->id,
        'loan_id'=> $loan->id,
        'plan_id'=> $plan->id,
        'reference'=> 'LWT-'.time().rand(1,7).$faker->randomElement(range(1,200)),
        'collection_date'=> now(),
        'collection_method'=> 'Default',
        'code'=> '036',
        'amount'=> $plan->total_amount,
        'description'=> 'Upload',
        'direction'=> 1,
        'is_logged'=> true
    ];
});
