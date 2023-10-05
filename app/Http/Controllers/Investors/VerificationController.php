<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\InvestorVerificationRequest;
use App\Models\Settings;

use App\Helpers\FinanceHandler;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:investor');    
    }
    
    public function index()
    {
        $investor = auth()->guard('investor')->user();
        if ($investor->is_verified)
            return redirect()->back()->with('failure', 'You have already been verified');
            
        return view('investors.verification.index', compact('investor'));
    }
    
    public function apply(Request $request, FinanceHandler $financeHandler)
    {
        $investor = auth()->guard('investor')->user();
        
        if($investor->hasPendingVerification()){
            return response()->json(
                [
                    'status' => 0, 
                    'message' => 'You have a request pending. Please wait for a response first!'
                ],
            400);
        }
        
        $rules = [
            'licence_type' => 'required|integer',
            'tax_number' => 'required|string',
            'licenceImage' => 'required|image|max:20000',
            'regCertificate' => 'nullable|image|max:20000'
        ];
        
        $this->validate($request, $rules);
        
        $managed_account = (boolean) $request->managed_account ?? 0;
        
        $data = [
            'investor_id' => $investor->id,
            'tax_number' => $request->tax_number,
            'licence_type' => $request->licence_type,
            'managed_account' => $managed_account,
            'status' => 2,
        ];
        
        $lender_fee = Settings::investorVerificationFee();
        
        if($investor->wallet < $lender_fee){
            return response()->json([
                'status' => 0,
                'message' => 'You have insufficient funds. Please fund your wallet!'
            ], 400);
        }
        
        $code = config('unicredit.flow')['investor_verification'];
        
        $financeHandler->handleSingle(
            $investor, 'debit', $lender_fee, null, 'W', $code
        );
        
        if ($request->hasFile('licenceImage') && $request->file('licenceImage')->isValid()) {
            $data['licence'] = $request->licenceImage->store('public/licences');
        }
        
        if ($request->hasFile('regCertificate') && $request->file('regCertificate')->isValid()) {
            $data['registration_certificate'] = $request->regCertificate->store('public/reg_certificates');
        }
        
        if(InvestorVerificationRequest::create($data)) {
            return response()->json([
                'status' => 1,
                'message' => 'Lenders activation request sent successfully'
            ], 200);
        }
        
        return response()->json([
                'status' => 0,
                'message' => 'An error occurred. Please try again'
        ], 200);
    }
}
