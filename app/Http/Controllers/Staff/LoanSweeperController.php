<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Traits\LoanSweepManager;
use App\Http\Controllers\Controller;

class LoanSweeperController extends Controller
{
    //
    use LoanSweepManager;
}
