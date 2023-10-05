<?php

namespace App\Http\Controllers\Admin;

use App\Models\Refund;
use App\Traits\Refunds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Events\RefundApprovedEvent;


class RefundController extends Controller
{	
	use Refunds;


	public function pendingRefund(){
		$data = Refund::with('getUSerInfo','loanInfo')->where('status', 0)->orderBy('id', 'desc')->get();
		return view('admin.refunds.pending')->with('refunds', $data);
	}

    public function log(){
    	$data = Refund::where('status', 1)->orwhere('status', 2)->orderBy('id', 'desc')->get();
        return view('admin.refunds.logs')->with('refunds', $data);
    }
}
