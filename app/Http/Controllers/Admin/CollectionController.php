<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Unicredit\Collection\LoanSweeper;

use App\Models\RepaymentPlan;

class CollectionController extends Controller
{
    private $sweeper;
    
    public function __construct(LoanSweeper $sweeper)
    {
        $this->sweeper = $sweeper;
    }
    
    public function tryCard(RepaymentPlan $repaymentPlan)
    {
        if (!$repaymentPlan->exists) abort(404);
        
        $attempt = $this->sweeper->makeCardAttempt($repaymentPlan);
        
        $type = $attempt['status'] ? 'success': 'failure';
        
        return redirect()->back()->with($type, $attempt['message']);
    }
    
    public function useDDM(RepaymentPlan $repaymentPlan)
    {
        if (!$repaymentPlan->exists) abort(404);
        
        $attempt = $this->sweeper->issueDebitOrder($repaymentPlan);
    
        if ($attempt->isASuccess()) {
            return redirect()->back()->with('success', 'Debit Order Issued Successfully');
        }
        
        return redirect()->back()->with('failure', $attempt->getMessage());
    }
}
