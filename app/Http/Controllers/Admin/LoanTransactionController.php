<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Traits\Managers\LoanTransactionManager;
use DB;

class LoanTransactionController extends Controller
{
    //

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
            DB::beginTransaction();
            
            $this->addLoanTransaction($request);
            
        } catch (\Exception | QueryException $e) {
            DB::rollback();
            $errMsg = isset($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage();
            return redirect()->back()->with('failure', $errMsg);
        }

        DB::commit();
        return redirect()->back()->with('success', ' Transaction was added successfully');
    }

}
