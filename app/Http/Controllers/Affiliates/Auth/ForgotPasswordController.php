<?php

namespace App\Http\Controllers\Affiliates\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Auth;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;
    
    protected function guard()
    {
        return Auth::guard('affiliate');
    }
    
    public function broker()
    {
      return Password::broker('affiliates');
    }
}
