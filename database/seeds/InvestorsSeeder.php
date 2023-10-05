<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Investor;

class InvestorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Investor::class, 20)->create();
    }
}
