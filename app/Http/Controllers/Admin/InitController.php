<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class InitController extends Controller
{
    public function initialize()
    {
        DB::table('admins')->insert([
            'name' => 'Unicredit Admin',
            'phone' => '08033172195',
            'email' => 'manager@unicredit.ng',
            'password' => bcrypt('Un1cr3d1t@18'),
        ]);
        
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
            'name' => 'Loan Request Application Fee',
            'slug' => 'loan_request_application_fee', 
            'value' => '2000'
        ]);
        return 'Done';
    }
}
