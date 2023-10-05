<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Paystack, DB;
use App\Models\BillingCard;
use App\Helpers\FinanceHandler;

class BillingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cards = $user->billingCards;
        return view('users.billing.index', compact('user', 'cards'));
    }
    
    public function addCard(Request $request)
    {
        $charge = 450;
        
        $paystackCharge = (0.015 * $charge * 100);
        $request->request->add(['reference' => Paystack::genTranxRef()]);
        $request->request->add(['key' => config('paystack.secretKey')]);
        $request->request->add(['amount' => ($charge * 100) + $paystackCharge]);
        $request->request->add(['tokenize' => true]);
        $request->request->add(['email' => auth()->user()->email]);
        $request->request->add(['callback_url' => route('users.billing.add-card')]);
        
        return Paystack::getAuthorizationUrl()->redirectNow();   
    }
    
    public function handleAddCard(FinanceHandler $financeHandler)
    {
        $data = Paystack::getPaymentData();
        
        // AUTH_06zs4ab0je
        $charge = 450;
        
        if ($data['status']) {
            $authorization = $data['data']['authorization'];
            try {
                DB::beginTransaction();
                
                $user = auth()->user();
               
                
                $user->billingCards()->create($this->extractBillingInfo($authorization));
                
                $code = config('unicredit.flow')['wallet_fund'];
        
                $financeHandler->handleSingle(
                    $user, 'credit', $charge, null, 'W', $code
                );
                
                $code = config('unicredit.flow')['card_set_up_fees'];
        
                $financeHandler->handleSingle(
                    $user, 'debit', $charge, null, 'W', $code
                );
                
                DB::commit();
                
                return redirect()->route('users.billing.index')
                    ->with('success', 'Card added successfully');
            } catch(\Exception $e) {
                //Log exception
                DB::rollback();
                return redirect()->route('users.billing.index')
                    ->with('failure', 'Error: ' . $e->getMessage());
            }
            // save authorization and log transaction
        }
        
        return redirect()->route('users.billing.index')
                ->with('failure', 'Card could not be set up');
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
    
    public function removeCard(BillingCard $billingCard)
    {
        try {
            $user = $billingCard->user;

            if ($user->activeLoans()->count() > 0) {

                throw new \Exception('You cannot delete card when you have an active loan');
            }

            if ($user->activeLoanRequest()->count() > 0) {
                throw new \Exception('You cannot delete card when you have an active or pending loan request');
            }

            $billingCard->delete();
            return redirect()->back()->with('success', 'Card removed successfully');
        } catch (\Exception $e) {
            
            return redirect()->back()->with('failure', "Error: " . $e->getMessage());
            
        }
    }
}
