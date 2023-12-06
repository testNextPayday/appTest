<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingsSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingsController extends Controller
{
    //
    public function settings(){
        $savings_settings = SavingsSettings::first();
        // dd($savings_settings);
        if($savings_settings == null){
            SavingsSettings::create([
                'interest_percent' => '2',
                'minimum_savings' => '1000', 
                'minimum_duration' => '2'    
            ]);
        }
        $savings_settings = SavingsSettings::first();

        return view('admin.savings.settings', compact('savings_settings'));
    }
}
