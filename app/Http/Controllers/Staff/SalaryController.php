<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalaryController extends Controller
{
    //

    public function index()
    {
        return view('staff.salary.index');
    }
}
