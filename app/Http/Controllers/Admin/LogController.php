<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Log as AppLog;
use App\Models\RepaymentPlan;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = [];
        
        if ($request->start_date || $request->end_date) {
            $logs = AppLog::query();
            
            if ($request->start_date)
                $logs = $logs->whereDate('created_at', '>=', $request->start_date);
            
            if ($request->end_date)
                $logs = $logs->whereDate('created_at', '<=', $request->end_date);
            
            $logs = $logs->latest()->get();
        }
        
        return view('admin.logs.index', compact('logs'));
    }
    
    public function getRepaymentLogs(Request $request)
    {
        $logs = [];
        
        if ($request->start_date || $request->end_date) {
            $logs = RepaymentPlan::query();
            
            if ($request->start_date)
                $logs = $logs->whereDate('payday', '>=', $request->start_date);
            
            if ($request->end_date)
                $logs = $logs->whereDate('payday', '<=', $request->end_date);
            
            $logs = $logs->latest()->get();
        }
        
        return view('admin.repayments.logs', compact('logs'));
    }
}
