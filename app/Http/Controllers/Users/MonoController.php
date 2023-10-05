<?php

namespace App\Http\Controllers\Users;

use DB;
use App\Models\Loan;
use App\Models\BankDetail;
use App\Models\LoanRequest;
use App\Models\MonoPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Exception\ClientException;
use App\Notifications\Users\LoanSetupNotification;
use App\Services\MonoStatement\BaseMonoStatementService;

class MonoController extends Controller
{
    protected $monostatementService;

    public function __construct(BaseMonoStatementService $bankStatementService)
    {
        $this->monostatementService = $bankStatementService;
    }

    public function submitRequest(){
        return view('users.mono.create');
    }

    public function paymentSetup(){
        return view('users.loans.verify-mono');
    }

    public function verifyStatus(Request $request){    
        try{
            DB::beginTransaction();    
            $reference = $request->reference;
            $this->monostatementService->verifyPaymentStatus($reference);
            $response = $this->monostatementService->getResponse();
            $status = $response['data']['status'];
            
            $account = $response['data']['account'];
            $startDate = $response['data']['startDate'];
            $endDate = $response['data']['endDate'];
            $user = Auth::user();
            
            $bankDetails = $user->banks->last();
        
            if($bankDetails){

                $this->monostatementService->checkBankInformation($account);            
                $response = $this->monostatementService->getResponse();            
                $accountNumber = $response['account']['accountNumber'];
                
                $loan = Loan::where('mono_payment_reference',$reference)->first();
                    
                    if(!$loan){
                        session()->flash("error", "This Link Has Expired");                        
                        return redirect()->back()->with('error', 'This Link Has Expired, See Notification Center For New Link ');
                    }else{
                        if($bankDetails->account_number == $accountNumber){
                                MonoPayment::create([
                                    'reference' => $reference,
                                    'verification_status'=>$status,
                                    'account_id'=>$account,
                                    'user_id' =>$user->id,
                                    'loan_id'=>$loan->id,
                                    'start_date'=>$startDate,
                                    'end_date'=>$endDate
                                ]);

                                if($status == 'active'){
                                    $code = 102;                            
                                    $value = 2;
                                    $loan->updateCollectionMethodStatus($code, $value);
                                    DB::commit(); 
                                    return redirect()->route('users.loan.setup-dashboard', [$loan]);
                                }

                                $loan = Loan::where('mono_payment_reference',$reference)->first();
                                $my_array = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                                $my_arrays = str_shuffle($my_array);
                                $my_arrayz = str_shuffle($my_array);
                                $reference = rand(0000,9000).substr($my_arrays,25).rand(0,9).rand(1000,9000).substr($my_arrayz,25);
                                $amount = $loan->emi;
                                //initiating mono payment
                                $this->monostatementService->initiatePayment($amount, $reference);
                                $monoInfo = $this->monostatementService->getResponse();
                                $reference = $monoInfo['reference'];
                                $paymentLink = $monoInfo['payment_link'];

                                $loan->update([
                                    'mono_payment_link'=>$paymentLink,
                                    'mono_payment_reference'=>$reference,
                                ]);
                                
                                $loan->user->notify(new LoanSetupNotification($loan));
                                
                                DB::commit(); 

                                $notifications = $request->user()->notifications;
                                return redirect()->route('users.notification.index', ['notifications'=>$notifications]);
                            
                        }else{
                                MonoPayment::create([
                                    'reference' => $reference,
                                    'verification_status'=>$status,
                                    'account_id'=>$account,
                                    'user_id' =>$user->id,
                                    'loan_id'=>$loan->id,
                                    'start_date'=>$startDate,
                                    'end_date'=>$endDate
                                ]);
                                $loan = Loan::where('mono_payment_reference',$reference)->first();
                                $my_array = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                                $my_arrays = str_shuffle($my_array);
                                $my_arrayz = str_shuffle($my_array);
                                $reference = rand(0000,9000).substr($my_arrays,25).rand(0,9).rand(1000,9000).substr($my_arrayz,25);
                                $amount = $loan->emi;
                                //initiating mono payment
                                $this->monostatementService->initiatePayment($amount, $reference);
                                $monoInfo = $this->monostatementService->getResponse();
                                $reference = $monoInfo['reference'];
                                $paymentLink = $monoInfo['payment_link'];

                                $loan->update([
                                    'mono_payment_link'=>$paymentLink,
                                    'mono_payment_reference'=>$reference,
                                ]);
                                
                                $loan->user->notify(new LoanSetupNotification($loan));
                                
                                session()->flash("error", "Bank Details Don't Match");
                                DB::commit();
                                return redirect()->back()->with("error","Your Mono Bank Details Do Not Match The Information On Our System. A New Setup Link Has Been Generated, Go To Notification Center and Try Again");
                        }
                    }
                    
                            
            }else{
                    session()->flash("error", "Please Add Bank Details To Your Profile");                        
                    return redirect()->back()->with("error", "Please Add Bank Details To Your Profile");
            }
        }
            
        catch(\Exception $e) {
            // Dont rollback if the env is testing            
            if (config('app.env') !== "testing")
                DB::rollback();
            // dd($e->getMessage());
            return redirect()->back()->with("failure", "Error: " . $e->getMessage());
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

    public function verifyBank($id){
        $user = Auth::user();
        $this->monostatementService->checkBankInformation($id);
        $bankInfo = $this->monostatementService->getResponse();             
        
        //Save Mono ID
        $bankDetails = $user->banks->last();
        
        if($bankDetails){
            if($bankDetails->bank_code != $bankInfo['account']['institution']['bankCode']){                
                return false;
            }
        }
        return true;
    }

    
    public function getStatement(Request $request){
        try{
            $id = $request->monoId;
            $this->monostatementService->statementRequest($id);
            $response = $this->monostatementService->getResponse();

            if($response){
               
                return response()->json([
                'status'=>true,
                'data'=> [
                    'statementPDF'=>$response['path']
                ],
                'message'=>'Bank Statement Retrieved Successfully'
               ]);
            }  
            return response()->json(['status'=>false, 'message'=>'Bank Statement Retrieval Failed', 422]);          
        }catch(\Exception $e){
            $data = [
                'status'=> false, 
                'message'=> $e instanceof ClientException ? json_decode((string)$e->getResponse()->getBody())->message : $e->getMessage()
            ];
            return response()->json($data, 500);
        }             
    }
}
