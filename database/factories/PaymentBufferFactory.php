<?php

namespace Database\Factories;

use App\Models\PaymentBuffer;
use Faker\Generator as Faker;

$factory->define(PaymentBuffer::class, function (Faker $faker) {
    return [
        //
        'amount'=>2000,
        'plan_id'=>function(){
            return factory(\App\Models\RepaymentPlan::class)->create()->id;
        },
        'transaction_ref'=> 'ivdmbiqu4vuqha6'
        
    ];
});
