<?php

namespace App\Http\Controllers\Affiliates\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Auth;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;
    
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/affiliates/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:affiliate');
    } 
    
    protected function broker()
    {
        return Password::broker('affiliates');
    }
    
    protected function guard()
    {
        return auth()->guard('affiliate');
    }
    
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.affiliates.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
