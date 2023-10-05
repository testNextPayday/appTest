<?php

namespace App\Http\Controllers\Affiliates;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:affiliate');
    }
    
    public function index()
    {
        $affiliate = auth('affiliate')->user();
        return view('affiliates.profile.index', compact('affiliate'));
    }
    
    public function update()
    {
        
        $affiliate = auth('affiliate')->user();
        
        // Prevents unwanted data from being passed through
        $data = collect(request()->all())
                    ->intersectByKeys($affiliate->toArray())
                    ->toArray();
                    
        if(request()->hasFile('avatar') && request()->file('avatar')->isValid()) {
            if(basename($affiliate->avatar) !== 'default.png') {
                //delete old avatar
                Storage::delete(Arr::get($affiliate->getAttributes(), 'avatar'));
            }
            $data['avatar'] = request('avatar')->store('public/avatars');
        }
        
        if(request()->hasFile('cv') && request()->file('cv')->isValid()) {
            if($affiliate->getOriginal('cv')) {
                //delete old avatar
                Storage::delete(Arr::get($affiliate->getAttributes(), 'cv'));
            }
            $data['cv'] = request('cv')->store('public/cvs/affiliates');
        }
                    
        if ($affiliate->update($data)) {
            return redirect()->back()->with('success', 'Update successful');
        }
        
        return redirect()->back()->with('failure', 'Update failure');
    }
    
    
    public function bankUpdate()
    {
        $affiliate = auth('affiliate')->user();
        $data = request()->only(['bank_code', 'account_number']);
        
        $data['bank_name'] = config('remita.banks')[$data['bank_code']];
        $data['recipient_code'] = null;
        
        if ($bank = $affiliate->bank) {
            $bank->update($data);
        } else {
            $bank = $affiliate->banks()->create($data);
        }
        
        return redirect()->back()->with('success', 'Update successful');
    }
    
    public function updatePassword(Request $request)
    {
        if (!(Hash::check($request->current_password, auth()->user()->password))) {
            // The passwords matches
            return redirect()->back()->with("failure", "Your current password does not match with the password you provided. Please try again.");
        }
 
        if(strcmp($request->current_password, $request->new_password) == 0){
            //Current password and new password are same
            return redirect()->back()->with("failure", "New Password cannot be same as your current password. Please choose a different password.");
        }
        
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
 
        //Change Password
        $user = auth()->user();
        $user->password = bcrypt($request->new_password);
        $user->save();
 
        return redirect()->back()->with("success", "Password changed successfully!");   
    }
}
