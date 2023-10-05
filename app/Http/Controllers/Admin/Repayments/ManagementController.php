<?php

namespace App\Http\Controllers\Admin\Repayments;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManagementController extends Controller
{
    public function index()
    {
        return view('admin.repayments.manage');
    }
}
