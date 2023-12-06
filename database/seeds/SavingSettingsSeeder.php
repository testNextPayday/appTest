<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SavingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('savings_settings')->insert([
            'interest_percent' => '2',
            'minimum_savings' => '1000', 
            'minimum_duration' => '2'    
        ]);
    }
}
