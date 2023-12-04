<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\WalletTransaction\CancelTransactionService;

class WalletTransactionController extends Controller
{

    public function index()
    {
        $transactions = WalletTransaction::with('owner')->latest()->paginate(300);
        return view('admin.wallet.index', compact('transactions'));
    }

    //
    
    /**
     * Cancel a transaction
     *
     * @param  \App\Models\WalletTransaction $walletTransaction
     * @return void
     */
    public function cancel(WalletTransaction $transaction, CancelTransactionService $cancelService)
    {
        try {

            DB::beginTransaction();

            $cancelService->cancelTransaction($transaction);

            DB::commit();

            return response()->json('Successfully cancelled');
            
        }catch (\Exception $e) {

            DB::rollback();

            return response()->json($e->getMessage(), 422);

        }
    }
}
