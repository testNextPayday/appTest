<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('staff')->insert([
            'firstname' => 'Chiemela',
            'lastname' => 'Chinedum',
            'midname' => 'Godswill',
            'phone' => '08034503911',
            'email' => 'melas.devsq@olotusquare.co',
            'password' => bcrypt('secret'),
            'avatar' => 'public/defaults/avatars/default.png',
            'reference' => Str::random(10)
        ]);
    }
}
