<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Settings;
use App\Models\Employer;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Settings::all();
        $employers = Employer::primary()->get();
        return view('admin.settings', compact('settings', 'employers'));
    }

    public function update(Request $request)
    {
        $data = $request->all();
        if ($data['_token']) unset($data['_token']);
        foreach ($data as $key => $value) {
            $setting = Settings::whereSlug($key)->first();
            $setting->update(['value' => $value]);
        }
        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
