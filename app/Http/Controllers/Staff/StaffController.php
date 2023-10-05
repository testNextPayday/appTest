<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Traits\EncryptDecrypt;
use Auth, Validator;
use App\Models\Staff;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:staff');
    }
    
    public function dashboard()
    {
        return view('staff.dashboard');
    }
    
}
    