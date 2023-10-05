<?php

namespace Database\Factories;

use Faker\Generator as Faker;
use App\Models\PenaltySetting;

$factory->define(PenaltySetting::class, function (Faker $faker) {
    return [
        //
        'entity_type'=> 'Test',
        'entity_id'=> 3,
        'value'=> 2,
        'grace_period'=> 10
    ];
});
