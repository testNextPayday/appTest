<?php

namespace App\Http\Controllers\Admin\PaydayNoteInvestors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InvestmentTransaction;
use App\Services\MonoStatement\BaseMonoStatementService;

class PaymentController extends Controller
{
    protected $monostatementService;

    public function __construct(BaseMonoStatementService $bankStatementService)
    {
        $this->monostatementService = $bankStatementService;
    }

    public function pendingPayment(){
        $payments = InvestmentTransaction::where('status', 0)->get();
        return view('admin.paydaynotes.pending-payments', compact('payments'));
    }

    public function approvedPayment(){
        $payments = InvestmentTransaction::where('status', 1)->get();
        return view('admin.paydaynotes.approved-payments', compact('payments'));
    }

    public function allPayment(){
        $payments = InvestmentTransaction::all();
        return view('admin.paydaynotes.all-payments', compact('payments'));
    }

    public function verifyMonoStatus(Request $request){
        try {
            $reference = $request->reference;
            $this->monostatementService->verifyPaymentStatus($reference);
            $response = $this->monostatementService->getResponse();
            $status = $response['data']['status'];        
            $transaction = InvestmentTransaction::where('reference', $reference)->first();            
           
            if($status == 'active'){
                $transaction->update(['verification_status'=>$stataus]);
                return response()->json(['status'=>true, 'message'=>'Your Payment Is Verified, Wait For Admin Approval'], 200);
            }
            return response()->json(['status'=>false, 'message'=>'Payment Status Could Not Be Verified'], 400);
        }catch(\Exception $e) {
            return response()->json(['status'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

    public function ApproveMonoPayments(Request $request){
        $investorId = Investor::where;
        
        $code = config('unicredit.flow')['wallet_fund'];
            $financeHandler->handleSingle(
                $person, 'credit', $request->amount, null, 'W', $code
            );

            if (($guard == 'investor')) {

               

                event(new InvestorWalletFundEvent($person, $request->amount));
                
                
            }
            
            return response()->json(['status' => 1, 'message' => 'Payment Successful'], 200);
    }
}
