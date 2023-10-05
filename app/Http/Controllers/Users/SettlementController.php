<?php

/**
 * This is the settlement controller for users it utilizes the 
 * settlementManager class
 */

namespace App\Http\Controllers\Users;

use PDF;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Unicredit\Contracts\PaymentGateway;
use App\Unicredit\Managers\SettlementManager;


class SettlementController extends Controller
{
    //

   
       
    /**
     * Gets the page for user to perform settlement action
     *
     * @param  mixed $request
     * @return void
     */
    public function settlement(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
        return view('users.loans.settle',['loan'=>$loan]);
    }
    
    /**
     * Make payment button action that calls the settlement manager
     *
     * @param  mixed $request
     * @return void
     */
    public function pay(Request $request, SettlementManager $manager)
    { 
        $callbackUrl  = route('users.settlement.payment_callback');
        return $manager->makePayment($request, $callbackUrl);
    }


    public function save(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
    }
    
    /**
     * Handles the callback endpoint
     * For settlement payments made by the user
     *
     * @return void
     */
    public function handleSettlementPaymentCallback(SettlementManager $manager)
    {
        $previousUrlString = 'users.apply.settlement';
        $loanUrlString = 'users.loans.view';
        return $manager->handlePaymentCallback($previousUrlString, $loanUrlString);

    }

   

    
}
