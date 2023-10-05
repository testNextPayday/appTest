<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Traits\Managers\LoanTransactionManager;

class LoanTransactionController extends Controller
{

    use LoanTransactionManager;
    //
    public function add(Request $request)
    {
        $this->validate($request, [
            'transaction_name' => 'required|string',
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'loan_id' => 'required',
        ]);

        try {
            
            $this->addLoanTransaction($request);
        } catch (\Exception | QueryException $e) {
            $errMsg = isset($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage();
            return redirect()->back()->with('failure', $errMsg);
        }

        return redirect()->back()->with('success', ' Transaction was added successfully');
    }
}
