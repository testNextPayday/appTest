<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AuthController extends Controller
{
   
    public function __construct()
    {
      $this->middleware('guest:investor', ['except' => ['logout']]);
    }
    
    public function showLoginForm()
    {
      return view('auth.investors.login');
    }
    
    public function login(Request $request)
    {
      // Validate the form data
      $this->validate($request, [
        'email'   => 'required|email',
        'password' => 'required|min:6'
      ]);
      
      $credentials = $request->only(['email', 'password']);
      
      if (Auth::guard('investor')->attempt($credentials, $request->remember)) {
        return redirect()->intended(route('investors.dashboard'));
      } 
      
      return redirect()->back()->withInput($request->only('email', 'remember'));
    }
    
    public function logout()
    {
        Auth::guard('investor')->logout();
        return redirect()->route('investors.signin');
    }
}