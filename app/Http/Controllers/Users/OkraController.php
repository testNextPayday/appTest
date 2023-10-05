<?php

namespace App\Http\Controllers\Users;

use DB;
use App\Models\BankDetail;
use App\Models\OkraRecord;
use App\Models\LoanRequest;
use App\Models\Loan;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;
use App\Services\Okra\OkraService;

class OkraController extends Controller
{
    
    
    public function __construct()
    {
        
    }

    public function okraWebhook(Request $request){
    try{
        DB::beginTransaction();
        //$okraService = new OkraService(new Client);
        $bankId = $request->bankId;        
        $customerId = $request->customerId;
        $recordId = $request->recordId;
        $accountId = $request->accountId;
        $accountNumber = $request->accountNumber;
        $loanId = $request->loanId;        
        $user = Auth::user();
        $bankDetails = $user->banks->last(); 
        $loan = Loan::find($loanId);
        if($bankDetails){
            //$okraService->getBankAccountDetails($accountId);
            //$accountInfo = $okraService->getResponse();
            //$balanceID = $accountInfo['data']['accounts'][0]['balance'];
            $data = [
                'bankId'=>$bankId,
                'customerId'=>$customerId,
                'recordId'=> $recordId,
                'account_id'=>$accountId, 
                //'balance_id'=>$balanceID,                      
                'user_id'=>$user->id
            ];

            if($bankDetails->account_number != $accountNumber){ 
                OkraRecord::create($data);               
                return response()->json(['status'=>false, 'message'=>'Your Bank Details Does Not Match The Information On Our System. 
                                Please, Select A Bank Account That Is Registered On Our System and Try Again', 422 ]);
            }
            else{                                
                $bankDetails->update(['okra_account_id'=> $accountId]);
                $code = 101;
                $value = 2;
                $loan->updateCollectionMethodStatus($code, $value);
                $okraUser = OkraRecord::where('account_id', $accountId)->first();                
                if(!$okraUser){
                   OkraRecord::create($data); 
                }                
                DB::commit(); 
                return response()->json(['status'=>true, 'message'=>'Okra Successfully Authenticated', 200 ]);               
            }                      
        } 
    }
        
        catch(\Exception $e) {
           // Dont rollback if the env is testing            
            if (config('app.env') !== "testing")
                DB::rollback();
            // dd($e->getMessage());
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
    }

    public function authAccount(Request $request){
        try{            
            DB::beginTransaction();
            //$okraService = new OkraService();
            $bankId = $request->bankId;
            $responseCode= $request->responseCode; 
            $customerId = $request->customerId;
            $creditAccountId = $request->creditAccnt;                     
            $debitAccountId = $request->debitAccnt;
            $paymentId = $request->paymentId;
            $paymentRef = $request->paymentRef;
            $amount = $request->amount;

            if($responseCode){
                $user = Auth::user();
                $bankDetails = $user->banks->last();                
                $loan = Loan::where('user_id', $user->id)->last();
                dd($loan);

                $data = [
                    'user_id'=>$user->id,
                    'bank_response'=>$responseCode,
                    'bankId'=>$bankId,
                    'customerId'=>$customerId,
                    'credit_account'=>$creditAccountId,
                    'debit_account'=>$debitAccountId,
                    'payment_id'=>$paymentId,
                    'payment_ref'=>$paymentRef,
                    'setup_fee'=>$amount
                ];

                OkraSetup::create($data);

                if($bankDetails){
                    $this->okraService->getBankAccountDetails($debitAccountId);
                    $accountInfo = $this->okraService->getResponse();
                    $accountNumber = $accountInfo['data']['account']['nuban'];
                    $balanceID = $accountInfo['data']['account']['balance'];

                    if($bankDetails->account_number != $accountNumber){                
                        return response()->json(['status'=>false, 'message'=>'Your Bank Details Does Not Match The Information On Our System. 
                                                Please, Select A Bank Account That Is Registered On Our System and Try Again', 422 ]);
                    }
                    else{
                        $bankDetails->update(['okra_account_id'=> $debitAccountId, 'okra_balance_id' => $balanceID]);
                        $collectionMethods = json_decode($loan->collection_methods) ?? [];
                        foreach($collectionMethods as $method){
                            if($method->code == 101){
                                $loan->update([
                                    'collection_methods' => json_encode(["status" => 2 ])                    
                                ]);
                            }                 
                        }
                        DB::commit();
                        return response()->json(['status'=>true, 'message'=>'Okra Successfully Authenticated' , 200 ]);
                    }
                }
            }
            
        }
        
        catch(\Exception $e) {
           // Dont rollback if the env is testing
            // This is because Remita fails with test params.
            // Remove if condition when fix is found
            if (config('app.env') !== "testing")
                DB::rollback();

            // dd($e->getMessage());
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }

    }


    public function checkMonoStatus(Request $request){

        try {
            $user = Auth::user();
            // $bankDetails = $user->banks
            //                ->where('mono_id', '!=', null)
            //                ->last();
            $bankDetails = $user->banks->last();


            if($bankDetails && isset($bankDetails->mono_id) && !empty($bankDetails->mono_id)){
                $mono_id = $bankDetails->mono_id;
                return response()->json([
                    'status'=>true,
                    'data'=> [
                        'id'=>$mono_id
                    ],
                    'message'=>'User Successfully Authenticated'
                    ]);
            }
            return response()->json(['status'=>false, 'message'=>'User Authentication Failed'], 200);

        }catch(\Exception $e) {
            return response()->json(['status'=>false, 'message'=>$e->getMessage()], 500);
        }
        
        
    }

    public function authHttpRequest(Request $request){
    try{
        $user = Auth::user();
        
        //Authenticate User on Mono API
        $code = $request->auth_code;       
        $this->monostatementService->authRequest($code);        
        $response = $this->monostatementService->getResponse();
        $id = $response['id'];               

        //Update Mono ID
        $bankDetails = $user->banks->last();        
        if($this->verifyBank($id) != false){
            $bankDetails->update(['mono_id'=>$response['id']]);
                return response()->json([
                    'status'=>true,
                    'data'=> [
                        'id'=>$response['id']
                    ],
                    'message'=>'User Successfully Authenticated'
                   ]);
        }     
                     
        return response()->json(['status' => false, 'message'=>'Please Select Bank Account That Is Registered On our System', 'code'=>'bank_failure'], 422);
    }
    catch(\Exception $e){
        $data = [
            'status'=> false, 
            'message'=> $e instanceof ClientException ? json_decode((string)$e->getResponse()->getBody())->message : $e->getMessage()
        ];
        return response()->json($data, 500);
    }
      
    }

    

    
    
}
