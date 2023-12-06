<?php

namespace Database\Seeders;

use App\Models\SavingsSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // UsersTableSeeder::class, 
            // SettingsTableSeeder::class,
            // AdminsTableSeeder::class,
            // StaffTableSeeder::class,
           

        ]);
        // SavingsSettings::create([
        //     'interest_percent' => '2',
        //     'minimum_savings' => '1000', 
        //     'minimum_duration' => '2'    
        // ]);

        DB::table('savings_settings')->insert([
            'interest_percent' => '2',
            'minimum_savings' => '1000', 
            'minimum_duration' => '2'    
        ]);
    }
}
