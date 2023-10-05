<?php

namespace App\Http\Controllers\Admin;

use App\Models\BankDetail;
use Illuminate\Http\Request;
use App\Traits\TransferControls;
use App\Http\Controllers\Controller;
use App\Models\BankStatementRequest;
use App\Unicredit\Contracts\MoneySender;

class TransferControlsController extends Controller
{
    //
    use TransferControls;

    public function __construct(MoneySender $channel)
    {
        $this->channel = $channel;
    }

    public function index()
    {
        $requests = BankStatementRequest::retrievable()->with('user')->get();
        return view('admin.payments.controls', ['requests'=> $requests]);
    }

    public function getGatewayBalanceHistory(Request $request)
    {
        try{

            $response = $this->getBalanceHistory($request->all());

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response);
    }


    public function checkGatewayPaymentBalance(Request $request)
    {

        try{

            $response = $this->checkPaymentBalance();

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response);
    }


    public function disableOtpRequirement(Request $request)
    {

        try{

            $response = $this->disableOtpTransfers();

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response['message']);
    }


    public function enableOtpRequirement(Request $request)
    {
        try{

            $response = $this->enableOtpTransfers();

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response['message']);
    }


    public function finalDisableOtpRequirement(Request $request)
    {
        try{

            $response = $this->disableOtpWithToken($request->all());

        }catch(\Exception $e){
                
            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response['message']);
    }


    public function createRecipient(Request $request, BankDetail $bank)
    {

        try{

            $owner = $bank->owner;
            
            $data = [
                'type'=>'nuban',
                'name'=>$owner->name ?? $owner->firstname.' '.$owner->lastname,
                'account_number'=>$bank->account_number,
                'bank_code'=>$bank->bank_code
            ];

            $response = $this->channel->createRecipient($data);

            if($response['status']){

                $bank->update(['recipient_code'=>@$response['data']['recipient_code']]);
            }

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response['message']);
    }



   

    
}
