<?php

namespace App\Http\Controllers\Affiliates\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function getLogin()
    {
        return view('auth.affiliates.login');
    }
    
    public function login(Request $request)
    {
        $rules = $this->validationRules();
        
        $this->validate($request, $rules);
        
        $credentials = $request->only(array_keys($rules));
        
        if (auth('affiliate')->attempt($credentials)) {
            return redirect()->route('affiliates.dashboard')
                            ->with('success', 'Login successful');
        }
        
        return redirect()->back()
                        ->with('failure', 'Login failed');
    }
    
    public function logout()
    {
        auth('affiliate')->logout();
        return redirect('/affiliates/login');
    }
    
    private function validationRules()
    {
        return [
            'email' => 'email|required',
            'password' => 'required'
        ];
    }
}
