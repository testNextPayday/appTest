<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        // $penaltiesOnLoan = Loan::with('repaymentPlans')->where('user_id', Auth::user()->id)->get();
        // dd($penaltiesOnLoan[0]->repaymentPlans->sum('emi_penalties'));
        // dd(Auth::user()->repaymentPlans->sum('emi_penalties'));
        // $plan = 15000;
        // $check = $plan + ($plan * 0.1/3);
        // dd($check);
        return view('users.dashboard');
    }

    public function repaymentNotification(Request $request){
        $reference = $request->reference;
        $loan = Loan::whereReference($reference)->first();
        $user = $loan->user;
        $body = [
            'email' => $user->email,
            'amount' => $loan->emi * 100, // change to Kobo            
        ];

        $chargeUrl = "/transaction/initialize";
        
        $this->paystackService->setHttpResponse($chargeUrl, "POST", $body);

        $response = $this->paystackService->getResponse();

        $reference = $response['data']['reference'];

        $verifyUrl = "/transaction/verify/{$reference}";
        $this->paystackService->setHttpResponse($verifyUrl, "GET", $data=[]);

        $response = $this->paystackService->getResponse();

        $authorization = $response['data']['authorization'];
        $user->billingCards()->create($this->extractBillingInfo($authorization));

        $code = config('unicredit.flow')['wallet_fund'];
        
        $financeHandler->handleSingle(
            $user, 'credit', $charge, null, 'W', $code
        );
    }

    public function extractBillingInfo($authorization)
    {
        return [
            'authorization_code'=>$authorization['authorization_code'],
            'bin'=>$authorization['bin'],
            'last4'=>$authorization['last4'],
            'exp_month'=>$authorization['exp_month'],
            'exp_year'=>$authorization['exp_year'],
            'card_type'=>$authorization['card_type'],
            'bank'=>$authorization['bank'],
            'country_code'=>$authorization['country_code'],
            'brand'=>$authorization['brand'],
            'reusable'=>$authorization['reusable'],
            'signature'=>$authorization['signature'],
        ];
    }
}
