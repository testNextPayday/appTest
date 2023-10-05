<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\BillingCard::class, function (Faker $faker) {
    $user = factory(App\Models\User::class)->create();
    return [
        //
        'user_id'=>$user->id,
        'authorization_code'=>'AUTH_ueyryr8493TEYE',
        'last4'=>'0123',
        'exp_month'=>'12',
        'exp_year'=>'2030',
        'channel'=>'card',
        'status'=>1,
        'reusable'=>1
    ];
});
