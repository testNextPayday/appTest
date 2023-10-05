<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Managers\BillsManager;


class BillsController extends Controller
{

    use BillsManager;
    
    //

    
    /**
     * Staff index page for bills creation
     *
     * @return void
     */
    public function index()
    {
        return view('staff.bills.index');
    }

    /**
     * Pending bills
     *
     * @return void
     */
    public function pending()
    {
        return view('staff.bills.pending');
    }
}
