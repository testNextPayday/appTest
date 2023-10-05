<?php

namespace App\Http\Controllers\Affiliates;

use DB;
use App\Models\Settings;

use App\Models\Affiliate;
use Illuminate\Http\Request;
use App\Helpers\FinanceHandler;
use App\Models\WithdrawalRequest;
use App\Http\Controllers\Controller;
use App\Unicredit\Payments\WithdrawalHandler;

class WithdrawalRequestController extends Controller
{
    public function index()
    {
        $withdrawalRequests = auth('affiliate')->user()->withdrawalRequests;
        return view('affiliates.withdrawals.index', compact('withdrawalRequests'));
    }
    
    public function store(Request $request, FinanceHandler $financeHandler)
    {
      
        try {

            DB::beginTransaction();
            
                $user = auth('affiliate')->user();

                $array = [
                    'amount'=>$request->amount
                    
                    ];
        
                    $this->withdrawer = $user;
        
                    $this->validateRequest($array);
        
                    $withdrawData = $array;
        
                    $withdrawal = $this->withdrawer->withdrawals()->create($withdrawData);
        
                    $code = config('unicredit.flow')['withdrawal'];
        
                    $financeHandler->handleDouble(
        
                        $this->withdrawer, $this->withdrawer, $array['amount'] , $withdrawal, 'WTE', $code
                    );
                            
        } catch(\Exception $e) {
            
            DB::rollback();

            return $this->sendExceptionResponse($e);
        }

        DB::commit();

        return redirect()->back()->with('success', 'Request placed successfully');
    }


    private function validateRequest($request)

    {
        // allows custom validation by the model involved
        if(method_exists($this->withdrawer,'validateWithdrawals')){

            return $this->withdrawer->validateWithdrawals($request);
        }

        throw new \BadMethodCallException(get_class($this->withdrawer." cannot validate withdrawals so cannot withdraw"));
       
        
    }
    
    
   
}
