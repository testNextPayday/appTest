<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\Refund::class, function (Faker $faker) {
        $user = factory(\App\Models\User::class)->create();
        $loan = factory(\App\Models\Loan::class)->create([
            'user_id'=>$user->id
        ]);
        $staff = factory(\App\Models\Staff::class)->create();

    return [
        //
        'user_id'=> $user->id,
        'loan_id'=> $loan->id,
        'amount'=> $faker->numberBetween($min = 10000, $max=50000),
        'reason'=>'Testin the reason',
        'staff_id'=>$staff->id,
        'status'=>0
    ];
});
