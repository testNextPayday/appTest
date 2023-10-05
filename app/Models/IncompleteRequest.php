<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Services\LoanRequest\UserLoanRequestService;

class IncompleteRequest extends Model
{
    protected $guarded = [];
    
    /**
     * Serialize and store the incomplete request
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string  $msg
     * @param  array $verifyResponse
     * @return void
     */
    public static function serializeLoanRequest($request, $msg, $verifyResponse)
    {

        self::clearOutIncompleteRequests();

        $loanRequestData = UserLoanRequestService::generateLoanRequestData($request);
        // Upfront payment extras
        $loanRequestData['upfront_charge'] = $request->upfront_charge;
        $loanRequestData['interest_sum'] = $request->interest_sum;
        $loanRequestData['total_mgt_fee'] = $request->total_mgt_fee;
        $loanRequestData['charge'] = $request->charge;
        
        $data = [
            'data'=> json_encode($loanRequestData),
            'status_message'=> $msg,
            'user_id'=> Auth::id(),
            'paid_status'=> $verifyResponse['status'],
            'trnx_reference'=> @@$verifyResponse['reference']
        ];

        return self::create($data);
    }

    
    /**
     * Clears all Incomplete loan requests for a particular user
     *
     * @return void
     */
    public static function clearOutIncompleteRequests()
    {
        return Auth::user()->incompleteRequests()->delete();
    }


    
}
