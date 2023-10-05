<?php

namespace App\Http\Controllers\Investors;

use App\Models\Investor;
use Illuminate\Http\Request;
use App\Helpers\FinanceHandler;
use App\Helpers\TransactionLogger;
use App\Helpers\FundTransferHandler;
use App\Http\Controllers\Controller;
use App\Structures\FundTransferRequest;

class WalletTransferController extends Controller
{
    //

    public function __construct(FundTransferHandler $transferHandler)
    {
        $this->transferHandler = $transferHandler;
    }

    public function initiateTransfer(Request $request)
    {
        try{
           
            $this->validate($request,[
                'amount'=>'required',
                'flow'=>'required'
            ]);
        
           $this->transferHandler->attempt(new FundTransferRequest($request));
           $investor = Investor::find($request->sender_id);
           
        }catch(\Exception $e){

            return response()->json(['failure'=>$e->getMessage()],400);
        }

        return response()->json($investor,200);
        
    }
}
