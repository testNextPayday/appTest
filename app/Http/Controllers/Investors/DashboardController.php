<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:investor');
    }
    
    public function index()
    {
        $investor = auth()->guard('investor')->user();
        return view('investors.dashboard', compact('investor'));
    }

   
}
