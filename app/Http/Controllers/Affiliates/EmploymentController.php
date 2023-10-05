<?php

namespace App\Http\Controllers\Affiliates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Traits\Managers\EmploymentManager;

class EmploymentController extends Controller
{
    use EmploymentManager;    
    
    
    public function __construct()
    {
        $this->middleware('auth:affiliate');
    }
}
