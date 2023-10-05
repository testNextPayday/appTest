<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Helpers\FinanceHandler;

use App\Models\WithdrawalRequest;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\Managers\WithdrawalManager;
use App\Unicredit\Payments\WithdrawalHandler;

class WithdrawalRequestController extends Controller
{

    use WithdrawalManager;
    
    public function pending()
    {
        $withdrawalRequests = WithdrawalRequest::whereStatus("1")->latest()->get();
        $title = 'Active Requests';
        return view('admin.withdrawals.index', compact('withdrawalRequests', 'title'));
    }
    
    public function approved()
    {
        $withdrawalRequests = WithdrawalRequest::whereStatus("2")->latest()->get();
        $title = 'Approved Requests';
        return view('admin.withdrawals.index', compact('withdrawalRequests', 'title'));
    }
    
    public function declined()
    {
        $withdrawalRequests = WithdrawalRequest::whereStatus("4")->latest()->get();
        $title = 'Declined Requests';
        return view('admin.withdrawals.index', compact('withdrawalRequests', 'title'));
    }
    
    public function all()
    {
        $withdrawalRequests = WithdrawalRequest::latest()->paginate(20);
        return view('admin.withdrawals.index', compact('withdrawalRequests'));
    }
   
    
    /**
     * View a withdrawal Request
     *
     * @param  mixed $request
     * @param  mixed $withdrawal
     * @return void
     */
    public function show(Request $request, WithdrawalRequest $withdrawal)
    {
        $owner = $withdrawal->requester;

        // Date of last approved withdrawal less than  or date user was created
        $createdAt = $withdrawal->created_at->toDateTimeString();
        $lastApprovedRequest = $owner->transactions()->getQuery()->whereTime('created_at', '<', $createdAt)->get()->where('code', '014')->last();
        if ($lastApprovedRequest) {
            $lastDate =  optional($lastApprovedRequest)->created_at->toDateTimeString();
        } else {
            $lastDate = $owner->created_at->toDateTimeString();
        }
        
       
        // Get wallet transactions since last withdrawal date
        $transactions = $owner->transactions()->getQuery()
                        ->where('purse', 1)
                        ->whereBetween('created_at', [$lastDate, $createdAt])->get();
        

        $payload = ['request'=> $withdrawal, 'transactions'=> $transactions];
       
        return view('admin.withdrawals.show', $payload);
    }
}
