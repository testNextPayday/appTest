<?php

namespace App\Http\Controllers\staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Traits\Refunds;

class RefundController extends Controller
{

	use Refunds;

	

    public function view(){
    	$data = Refund::where('status', 1)->orwhere('status', 2)->orwhere('status', 0)->orderBy('id', 'desc')->get();
    	return view('staff.refund.show')->with('refunds', $data);
	}
	
	public function pendingRefund(){
		$data = Refund::with('getUSerInfo','loanInfo')->where('status', 0)->orderBy('id', 'desc')->get();
		return view('staff.refund.pending')->with('refunds', $data);
	}

   
}
