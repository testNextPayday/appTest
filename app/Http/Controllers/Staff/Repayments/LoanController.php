<?php

namespace App\Http\Controllers\Staff\Repayments;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RepaymentPlan;
use App\Models\NewRepayment;
use Auth;
use Paystack;

class LoanController extends Controller
{
    //
    public function index(User $user, Loan $loan){
        $loan = Auth::guard('staff')->user()->loans()->whereReference($reference)->first();
        
        $borrower =  User::where('reference',$user)->get()->first();
        
        return view('staff.repayment.new',compact('borrower','loan'));
    }
    public function store(Request $request){
        if($request->payment_method == 'paystack'){
            return $this->handleWithPaystack($request);
        }
            
        $string = request('repayments')[0];
        $repayments = explode (",", $string); 
        
        foreach($repayments as $repayment){
            $this->repay($repayment);
        }
        
        return redirect()->back()->with('success','Successfully repaid loans');
        
    }
    public function repay($plan){
        try{
            $paymentProof = request()->file('payment_proof') == null ? null : request()->file('payment_proof')->store('paymentproof',['disk' => 'public']);
            $repayment = RepaymentPlan::find($plan);
           
            $repayment->update([
               'collection_mode' => request('payment_method'),
               'payment_proof' => $paymentProof,
               'date_paid' => now(),
               'has_upload' => true
            ]);
        
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function paystackRepay($plan){
        try{
            $repayment = RepaymentPlan::find($plan);
           
            $repayment->update([
               'collection_mode' => request('payment_method'),
               'payment_proof' => null,
               'date_paid' => now(),
               'status' => true,
               'has_upload' => true
            ]);
        
        }catch(Exception $e){
            return $e->getMessage();
        }
    }
        
    public function showAll($reference){
        $loan = Auth::guard('staff')->user()->loans()->whereReference($reference)->first();
        $repayments = NewRepayment::where('is_confirmed',true);
        return view('staff.repayment.all',compact('repayments','loan'));
    }
    
    public function delete($id){
        $repayment = NewPayment::find($id);
        $repayment->delete();
        return redirect()->back()->with('success','Deleted successfully');
    }
    
    public function handleWithPaystack(Request $request)
    {
        // $request->request->add(['email' => 'yarrowbradley@gmail.com']);
        $request->request->add(['callback_url' => route('staff.paystack.payment')]);
        
        // $request->request->remove('reference');
        // dd(request()->all());
        request()->reference = null;
        
        return Paystack::getAuthorizationUrl()->redirectNow();
    }
    
   
    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
        $string = $paymentDetails['data']['metadata'][0];
        $repayments = explode (",", $string); 
        if($paymentDetails['data']['status'] == 'success'){
            foreach($repayments as $repayment){
                $this->paystackRepay($repayment);
            }
            return redirect()->back();
        }
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
    }
}
