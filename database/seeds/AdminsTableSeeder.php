<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'name' => 'Admin Admin',
            'phone' => '08034503911',
            'email' => 'melas.devsq@olotusquare.co',
            'password' => bcrypt('secret'),
        ]);
    }
}
