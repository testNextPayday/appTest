<?php

namespace App\Http\Controllers\Affiliates\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Affiliate;

class RegisterController extends Controller
{
    public function getRegister()
    {
        return view('auth.affiliates.register');
    }
    
    public function register(Request $request)
    {
        $rules = $this->validationRules();
        
        $this->validate($request, $rules);
        
        $data = $request->only(array_keys($rules));
        
        $data['cv'] = $request->cv->store('public/cvs/affiliates');
        
        $affiliate = Affiliate::create($data);

        // allow free registration
        $affiliate->update(['verification_applied'=> true]);
        
        $credentials = [
            'email' => optional($affiliate)->email, 
            'password' => $request->password
        ];
        
        $authentication = auth('affiliate')->attempt($credentials);
        
        if ($authentication) {
           return redirect()->route('affiliates.dashboard')
                            ->with('success', 'Registration successful');
        }
        
        return redirect()->back()
                        ->with('failure', 'Registration failed. Please try again');
        
    }
    
    private function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:affiliates,email|max:255',
            'password' => 'required|confirmed|min:6',
            'phone' => 'required',
            'address' => 'required',
            'state' => 'required',
            'cv' => 'required|file|mimes:pdf,png,jpg,jpeg|max:2048'
        ];
    }
    
}
