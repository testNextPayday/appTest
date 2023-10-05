<?php

namespace Database\Factories;

use Faker\Generator as Faker;

$factory->define(App\Models\TicketMessage::class, function (Faker $faker) {
    return [
        //
        'ticket_id'=> factory(\App\Models\Ticket::class)->create()->id,
        'owner_id'=> factory(\App\Models\User::class)->create()->id,
        'owner_type'=>'App\Models\User',
        'message'=> $faker->sentence
    ];
});
