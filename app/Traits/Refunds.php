<?php
namespace App\Traits;

use App\Models\User;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\RefundApprovedEvent;
use App\Models\LoanWalletTransaction;
use App\Traits\ReferenceNumberGenerator;


trait Refunds 
{

	use ReferenceNumberGenerator;

	protected $refPrefix = 'UC-LWT-';

	public function update(Request $request, $id, $status){

		$this->validate($request, [
			'user_id' => $request->user_id == null ? '' : 'required',
			'loan_id' => $request->loan_id == null ? '' : 'required',
			'amount'  => $request->amount  == null ? '' : 'required'
		]);
		
		try{

			DB::beginTransaction();

			$refund = Refund::find($id);

			if(!$refund){
				return redirect()->back()->with('failure',' Refund Not Found');
			}

			switch($status){

				case 1 :
					
					$refund->update(['status'=>1]);
					
					event(new RefundApprovedEvent($refund));
					DB::commit();
					return redirect()->back()->with('success', 'Refund approved successfuly');

				break;
				
				case 0 :
					if($refund->wallet_debited) {
						throw new \Exception('Wallet has been debited already decline refund');
					}
					$refund->update($request->only(['loan_id','user_id','amount','reason']));
					DB::commit();
					return redirect()->back()->with('success', 'Refund updated successfuly');
				
				break;

				case 2 : 
					$refund->update(['status'=>2]);
					if($refund->wallet_debited) {
						$user = $refund->user;
						LoanWalletTransaction::create([
							'amount'=> $refund->amount,
							'loan_id'=> $refund->loan_id,
							'confirmed'=>1,
							'description'=> $refund->reason,
							'reference'=>$this->generateReference('App\Models\LoanWalletTransaction'),
							'user_id'=>$user->id,
							'collection_method'=> 'Refunds',
							'code'=> '036',
							'status'=> 2,
							'collection_date'=> now(),
							'direction'=> 1 // inflow
						]);
						$loanWallet = $user->loan_wallet + $refund->amount;
						$user->update(['loan_wallet'=>$loanWallet]);
					}
					DB::commit();
					return redirect()->back()->with('success', 'Refund rejected successfuly');
				
				break;

				default :
					
					return redirect()->back()->with('failure', 'No Action specified');
			}// end switch

			
			

		}catch(\Execption $e){

			DB::rollback();
			
			return $this->sendExceptionResponse($e);
		}
		
	}
	
	public function store(Request $request)
    {	
		
		
    	$this->validate($request, [
			'amount'  => 'required',
			'user_id' => 'required',
			'loan_id' => 'required',
			'staff_id' => $request->staff_id == null ? '' : 'required'
		]);
		
       try{

		   $user = User::find($request->user_id);

		   if($request->amount > $user->masked_loan_wallet) {
			   throw new \Exception('Refunds Can only be made on money in the loan wallet');
		   }
		   LoanWalletTransaction::create([
			'amount'=> $request->amount,
			'loan_id'=> $request->loan_id,
			'confirmed'=>1,
			'description'=> $request->reason,
			'reference'=>$this->generateReference('App\Models\LoanWalletTransaction'),
			'user_id'=>$user->id,
			'collection_method'=> 'Refunds',
			'code'=> '036',
			'collection_date'=> now(),
			'status'=> 2,
			'direction'=> 2 // outflow
		   ]);

		   $loanWallet = $user->loan_wallet - $request->amount;

		   $user->update(['loan_wallet'=> $loanWallet]);

       		 $refund = Refund::create([
			'user_id'  => $request->user_id,
			'loan_id'  => $request->loan_id,
			'staff_id' => $request->staff_id == null ? 0 : $request->staff_id,
			'amount'   => $request->amount,
			'reason'   => $request->reason,
			'status'   => $request->user('admin') ? 1 : 0,
			'wallet_debited'=> true
				]);

				
				
			if($refund->status == 1){

				event(new RefundApprovedEvent($refund));
			}
	

       }catch(\Exception $e){

       	return redirect()->back()->with('failure', $e->getMessage());
       }

       return redirect()->back()->with('success','Refund was successfully created');
    }


  
}
?>