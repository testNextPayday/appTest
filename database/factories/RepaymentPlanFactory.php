<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\RepaymentPlan::class, function (Faker $faker) {

    $loan = factory(App\Models\Loan::class)->create();
    return [
        //
        'loan_id'=>$loan->id,
        'interest'=>$faker->numberBetween($min = 1000, $max=5000),
        'emi'=>$faker->numberBetween($min = 1000.67, $max=5000.88),
        'principal'=>$faker->numberBetween($min = 1000.67, $max=5000.88),
        'management_fee'=>$faker->numberBetween($min = 100, $max=5000),
        'balance'=>$faker->numberBetween($min = 1000.67, $max=5000.88),
        'month_no'=>$faker->randomElement(range(1,12)),
        'order_issued'=>false,
        'rrr'=>null,
        'date_paid'=>null,
        'has_issues'=>false,
        'transaction_ref'=>null,
        'requestId'=>null,
        'status_message'=>null,
        'collection_mode'=>null,
        'issues'=>null,
        'paid_out'=>false,
        'cancelled'=>false,
        'should_cancel'=>false,
        'invoice'=>null,
        'has_upload'=>false,
        'is_new'=>$faker->randomElement([true,false]),
        'begin_balance'=>$faker->numberBetween($min = 1000.67, $max=5000.88),
        'payments'=>$faker->numberBetween($min = 1000.67, $max=5000.88),
        'wallet_changed'=>false,
        'payday'=>$faker->dateTime($max = 'now', $timezone = null)
    ];
});
