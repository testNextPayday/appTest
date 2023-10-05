<?php

namespace App\Http\Controllers\Investors\Auth;

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
    protected $redirectTo = '/investor';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:investor');
    } 
    
    protected function broker()
    {
        return Password::broker('investors');
    }
    
    protected function guard()
    {
        return Auth::guard('investor');
    }
    
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.investors.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
