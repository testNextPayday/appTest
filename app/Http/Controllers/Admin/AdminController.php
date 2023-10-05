<?php

namespace App\Http\Controllers\Admin;

use Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\EncryptDecrypt;
use App\Http\Controllers\Controller;


class AdminController extends Controller
{
    
    
    public function __construct()
    {
        // $this->middleware('auth:admin');
    }
    
    public function dashboard()
    {
        
        return view('admin.dashboard');
    }
    
}
    