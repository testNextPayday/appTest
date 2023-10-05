<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\LoanFund;
use App\Models\WalletTransaction;
use App\Helpers\FinanceHandler;
use Carbon\Carbon;
use DB;

class LoanMigrationController extends Controller
{
    public function index(){
    	$data = Investor::with(['loanFunds' => function($q){
    		$q->select('reference', 'amount', 'investor_id', 'created_at')->where('status', 2)->orderBy('created_at', 'asc');
    	}])->get(['id', 'wallet', 'name', 'reference']);
   
    	return view('admin.investors.loan-migration')->with('investors', $data);
    }

    public function getSelectedFundCurrentValue(Request $request, LoanFund $fund)
    {	
    	$selectedfunds = json_decode($request->selectedLoanFund);
    	
    	$total = 0;
    	foreach ($selectedfunds as $selectedfund) {
    		$data = $fund::whereReference($selectedfund->reference)->first();
    		$total += $data->currentValue;
    	}

    	return $total;
    }

    public function migrate(Request $request, LoanFund $fund, FinanceHandler $financeHandler){
   
    	$to   = json_decode($request->to);
    	$from = json_decode($request->from);
    	$loanFundCurrentValue = json_decode($request->loanFundCurrentValue);
		$selectedLoanFunds = json_decode($request->selectedLoanFund);
		
    	try{

    		DB::beginTransaction();

    		if ($request->loanFundCurrentValue < 1) {
    			throw new \Exception("Error amount is null.");	
    		}

    		if (! $request->loanFundCurrentValue == $this->getSelectedFundCurrentValue($request, $fund)) {
    			throw new \Exception("Error amount mismatch");
    		}

    		if (! $to->id == $from->id) {
    			throw new \Exception("Error Processing Request for same user! ");
    		}

    		
    		$buyer = Investor::whereReference($to->reference)->first();
    		$code  = config('unicredit.flow')['investor_loanfund_migration'];

    		foreach ($selectedLoanFunds as $selectedLoanFund) {
    			$loanFund = $fund::whereReference($selectedLoanFund->reference)->first();
    			
  
    			$financeHandler->handleDouble(
    				$buyer, $loanFund->investor, $loanFund->currentValue, $loanFund, 'WTW', $code
    			);

    			// create a new asset for the buyer
    			$this->createChildAsset($loanFund, $to);

    			$loanFund->update([
    				'status' => 5, 
    				'sale_amount' => $loanFund->currentValue,
    				'transfer_date' => Carbon::now()
    			]);
    		}
    	
    	  DB::commit();
    	}catch(\Exception $e){
    		DB::rollback();

    		return response()->json(['failure'=>$e->getMessage()], 422);
    	}

    	return response()->json(['success'=>'Successful'],200);
    }

    private function createChildAsset(LoanFund $fund, $to){

    	$data = [
    		'investor_id' => $to->id,
            'request_id' => $fund->request_id,
            'percentage' => $fund->percentage,
            'amount' => $fund->currentValue,
            'original_id' => $fund->id,
            'status' => 2
    	];

        return LoanFund::create($data);
    }

}
