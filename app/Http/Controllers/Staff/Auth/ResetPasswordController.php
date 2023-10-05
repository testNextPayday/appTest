<?php

namespace App\Http\Controllers\Staff\Auth;

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
    protected $redirectTo = '/staff';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:staff');
    } 
    
    protected function broker()
    {
        return Password::broker('staffs');
    }
    
    protected function guard()
    {
        return Auth::guard('staff');
    }
    
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.staff.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
