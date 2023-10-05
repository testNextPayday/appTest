<?php

namespace App\Http\Controllers\Users;

use Carbon\Carbon;
use App\Models\Loan;
use App\Helpers\Constants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoanSetupController extends Controller
{
    //


    public function __construct()
    {
        //$this->middleware('signed');
    }


    public function setupFormDashboard(Loan $loan)
    {
        return view('users.loans.loan_setup', compact('loan'));
    }


    public function showSetupForm(Loan $loan)
    {       
        return view('users.offline.loan_setup', compact('loan'));
    }


    public function verifyUserCardSetup(Request $request,Loan $loan)
    {
        try{
            $response =  $this->verifyPayment($request);

            if($response['data']['status'] === 'success'){

                // save billing information
                $authorization = $response['data']['authorization'];

                $billingData = [
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
                // validate billing card
                $this->validateBillingCard($billingData,$loan);
    
                $loan->user->billingCards()->create($billingData);

                $loan->updateCollectionMethodStatus(Constants::CARDS_PAYSTACK, 2);

                return response()->json(['status'=> true , 'message'=>$response['data']['gateway_response']]);

            }

            return response()->json(['status'=>false,'message'=>$response['data']['gateway_response']]);

        }catch(\Exception $e){

            return response()->json(['status'=>false,'message'=>$e->getMessage()]);
        }

        
    }



    private function verifyPayment(Request $request) 
    {
        $reference = $request->reference;    
        
        $result = array();
        //The parameter after verify/ is the transaction reference to be verified
        $url = 'https://api.paystack.co/transaction/verify/' . $reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
          $ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . config('paystack.secretKey')]
        );
        $request = curl_exec($ch);
        curl_close($ch);
        $result = [];
        if ($request) {
          $result = json_decode($request, true);
        }
        return $result;
    }


    private function validateBillingCard($cardInfo,$loan)
    {

        
        if($cardInfo['reusable'] != true){

            throw new \DomainException(" Your card authorization must be reusable to complete setup");
        }
        
        if($loan->repaymentPlans->isNotEmpty()){

            $loanExpiryDate = $loan->repaymentPlans->last()->payday->format("Y m");

        }else{

            $loanCreatedDate = Carbon::parse($loan->created_at)->addMonths($loan->duration);

            $loanExpiryDate = $loanCreatedDate->format("Y m");
        }
        

        $cardExpiryDate = $cardInfo['exp_year']." ".$cardInfo['exp_month'];

        if($cardExpiryDate <= $loanExpiryDate){

            throw new \DomainException("This card will expire before the loan");
        }

        return true;
    }
}
