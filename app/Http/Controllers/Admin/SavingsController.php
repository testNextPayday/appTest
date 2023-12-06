<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SavingsSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingsController extends Controller {
    //
    public function settings() {
        $savings_settings = SavingsSettings::first();
        // dd($savings_settings);
        if($savings_settings == null) {
            SavingsSettings::create([
                'interest_percent' => '2',
                'minimum_savings' => '1000',
                'minimum_duration' => '2'
            ]);
        }
        $savings_settings = SavingsSettings::first();

        return view('admin.savings.settings', compact('savings_settings'));
    }

    public function update(Request $r) {

        $r->validate([
            'interest_percent' => 'required',
            'minimum_savings' => 'required',
            'minimum_duration' => 'required',
        ]);
        
        $savings_settings = SavingsSettings::first();
        $savings_settings->interest_percent = $r->interest_percent;
        $savings_settings->minimum_savings = $r->minimum_savings;
        $savings_settings->minimum_duration = $r->minimum_duration;
        $savings_settings->save();

        return back()->with('success', 'Savings settings updated successfully');
    }
}
