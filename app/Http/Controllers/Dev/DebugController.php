<?php

namespace App\Http\Controllers\Dev;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Loan;
use App\Models\RepaymentPlan;
use App\Models\Investor;

use App\Remita\DDM\CollectionChecker;

use App\Unicredit\Collection\RepaymentManager;

use App\Helpers\FinanceHandler;

use Log, DB;

class DebugController extends Controller
{
    public function credit(FinanceHandler $financeHandler)
    {
        $code = config('unicredit.flow')['wallet_fund'];
        
        $person = Investor::find(1);
        
        $financeHandler->handleSingle(
            $person, 'credit', 100000, null, 'W', $code
        );
        
        return "Done";
    }
}
