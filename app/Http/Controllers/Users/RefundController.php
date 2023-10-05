<?php

namespace App\Http\Controllers\Users;

use App\Traits\Refunds;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
     //
     use Refunds;


    public function __construct()
    {
        
    }
   
    public function index()
    {
       
        $refunds = Auth::user()->refunds;
       
        return view('users.refund.index', ['refunds'=>$refunds]);
    }
}
