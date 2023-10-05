<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'name' => 'Monthly Management Fee',
            'slug' => 'monthly_management_fee', 
            'value' => '1.5'    
        ]);
        
        DB::table('settings')->insert([
            'name' => 'Default Interest Percentage',
            'slug' => 'default_interest_percentage', 
            'value' => '10'    
        ]);
        
        DB::table('settings')->insert([
            'name' => 'Lenders Activation Fee',
            'slug' => 'lender_activation_fee', 
            'value' => '2000'
        ]);
        
        DB::table('settings')->insert([
            'name' => 'Ledger Balance',
            'slug' => 'ledger_balance', 
            'value' => '2000'
        ]);
        
        DB::table('settings')->insert([
            'name' => 'Employer Verification Fee',
            'slug' => 'employer_verification_fee', 
            'value' => '2000'
        ]);
        
        DB::table('settings')->insert([
            'name' => 'Loan Request Processing Fee',
            'slug' => 'loan_request_processing_fee', 
            'value' => '2000'
        ]);
        
        DB::table('settings')->insert([
            'name' => 'Affiliate Commission Rate',
            'slug' => 'affiliate_commission_rate', 
            'value' => '5'
        ]);
        
        DB::table('settings')->insert([
            'name' => 'Affiliate Minimum Withdrawal',
            'slug' => 'affiliate_minimum_withdrawal', 
            'value' => '10000'
        ]);
        
        DB::table('settings')->insert([
            'name' => 'Affiliate Verification Fee',
            'slug' => 'affiliate_verification_fee', 
            'value' => '5000'
        ]);
    }
}
