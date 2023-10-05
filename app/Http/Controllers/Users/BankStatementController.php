<?php

namespace App\Http\Controllers\Users;

use App\Models\User;
use App\Models\Settings;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\BankStatementRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\ClientException;
use App\Services\BankStatement\BankStatementService;
use App\Services\BankStatement\IBankStatementService;

class BankStatementController extends Controller
{
    //


    protected $statementService;

    
    /**
     * Injects the bank statement service into this controller
     *
     * @param  mixed $bankStatementService
     * @return void
     */
    public function __construct(IBankStatementService $bankStatementService)
    {
        $this->statementService = $bankStatementService;
    }

        
    /**
     * Make a request to retrieve bank statement
     *
     * @param  mixed $request
     * @return void
     */
    public function requestStatement(Request $request)
    {

       try {

          // Check if the user just previously requested a statement in the past 1day

          $recentRetreival = $this->statementService->userCurrentlyRetrievedStatement($request);

          if ($recentRetreival['status'] == true) {

                return response()->json(['status'=>'007', 'result'=>$recentRetreival['result']]);
          }

            $data = $this->prepareStatementRequestData($request);

            $response = $this->statementService->makeStatementRequest($data);

            $this->checkUnsuccessful($response);

            if ($this->statementService->isSuccessful()) {

                $user = User::whereReference($request->reference)->first();
                $response['user_id'] = $user->id;
                $bankDetails = $user->banks->last();
                $response['account_number'] = $bankDetails->account_number;
                $response['bank_name'] = $bankDetails->bank_name;
                $this->statementService->storeStatementRequest($response);
    
                return response()->json(['success'=> 'Request Sent successfully', 'requestID'=> $response['result'], 'status'=>'00']);
            }

            //return response()->json(['failure'=> $response['data']['message']]);

       }catch(\Exception $e) {

            if (env('APP_DEBUG')) {
                
                return response()->json([$e->getMessage(). 'at '. $e->getLine(). ' File'. $e->getFile()], Response::HTTP_BAD_REQUEST);
            }

            return $this->sendJsonErrorResponseB($e);

            
       }
    }

    
    /**
     * checkStatementByID
     *
     * @param  mixed $request
     * @return void
     */
    public function checkStatementByID(Request $request)
    {
        try {

            $data  = ['requestId'=> $request->requestID];

            $response = $this->statementService->checkFeedBackByRequestID($data);

            $this->checkUnsuccessful($response);

            $messageStatus = $response['result']['status'];
           
            $returnedResponse = [];

            if (strtolower($messageStatus) == 'ticket' || strtolower($messageStatus) == 'ticketsent') {

                // move the status to 1 saying ticket has been sent to customer
                $requestID = $data['requestId'];
                $this->statementService->updateStatementRequestStatus($requestID, 'ticketSent', $response);
                $returnedResponse['ticketsent'] = true;
                $returnedResponse['result'] = $response['result'];


                return response()->json($response);
            }

            return response()->json($response);
            
        }catch (\Exception $e) {

           

            if (env('APP_DEBUG')) {
                
                return response()->json([$e->getMessage(). 'at '. $e->getLine(). ' File'. $e->getFile()], Response::HTTP_BAD_REQUEST);
            }

            return $this->sendJsonErrorResponseB($e);
        }
    }


    public function confirmStatement(Request $request)
    {
        try {

            $requestID = $request->requestID;

            $statement = BankStatementRequest::where('request_id', $requestID)->first();

            if ($statement) {

                $statement->update(['ticket_no'=> $request->ticketNo, 'password'=> $request->password]);
            }

            $data = $request->only('ticketNo', 'password');

            $response = $this->statementService->confirmStatementRequest($data);
       
            $this->checkUnsuccessful($response);
            return response()->json($response);

        }catch(\Exception $e) {
            
            if (env('APP_DEBUG')) {
                
                return response()->json([$e->getMessage(). 'at '. $e->getLine(). ' File'. $e->getFile()], Response::HTTP_BAD_REQUEST);
            }

           

            return $this->sendJsonErrorResponseB($e);
        }
    }

    protected function sendJsonErrorResponseB($e, $statusCode = 422)
    {
       
        if ($e instanceof ClientException) {

            $err = json_decode((string)$e->getResponse()->getBody());
            $msg = $err->message;
            
        } else {

            Log::channel('unexpectederrors')->debug(
                $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine()
            );

            $msg = $e->getMessage();
        }

        return response()->json($msg, $statusCode);
    }



    public function checkStatementByTicketNo(Request $request)
    {
        try {

            $data  = ['ticketNo'=> $request->ticketNumber];

            $response = $this->statementService->checkFeedBackByRequestTicketNo($data);

            $this->checkUnsuccessful($response);

            return response()->json($response);
            
        }catch (\Exception $e) {

            if (env('APP_DEBUG')) {
                
                return response()->json([$e->getMessage(). 'at '. $e->getLine(). ' File'. $e->getFile()], Response::HTTP_BAD_REQUEST);
            }

            return $this->sendJsonErrorResponseB($e);
        }
    }


    
    
    /**
     * Not implemented in the program
     *
     * @param  mixed $request
     * @return void
     */
    public function reConfirmStatement(Request $request)
    {
        try {

            $data  = ['requestID'=> $request->requestID];

            $response = $this->statementService->reConfirmStatementRequest($data);

            $this->checkUnsuccessful($response);

            return response()->json($response);
            
        }catch (\Exception $e) {

            if (env('APP_DEBUG')) {
                
                return response()->json([$e->getMessage(). 'at '. $e->getLine(). ' File'. $e->getFile()], Response::HTTP_BAD_REQUEST);
            }

            return $this->sendJsonErrorResponseB($e);
        }
    }

    

    public function retrieveStatement(Request $request)
    {
        try {

            $data  = ['ticketNo'=> $request->ticketNumber];

            $response = $this->statementService->getPDFStatement($data);
            
            $this->checkUnsuccessful($response);

            if (isset($response['result'])) {

                $url = 'public/loan_requests/bank_statements/';

                $stringData  = base64_decode($response['result']);
                $statementRequest = BankStatementRequest::where('ticket_no', $request->ticketNumber)->first();            

                $filename = 'password-('.$statementRequest->password.')-'.time().bin2hex(random_bytes(6)).'.pdf';

                $fullPath = $url.'/'.$filename;

                Storage::disk('local')->put($fullPath, $stringData);

                //$pathstring = $imageService->saveEncodedString($stringData, $url);
                $statementRequest->update(['request_doc'=> $fullPath]);

                $response['result'] = asset(Storage::url($fullPath));
            }
          return response()->json($response);            
        }catch (\Exception $e) {

            if (env('APP_DEBUG')) {
                
                return response()->json([$e->getMessage(). 'at '. $e->getLine(). ' File'. $e->getFile()], Response::HTTP_BAD_REQUEST);
            }

            return $this->sendJsonErrorResponseB($e);
        }
    }

    
    /**
     * checkRetrievalMethod
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse $response
     */
    public function checkRetrievalMethod(Request $request)
    {
        try {
            // TODO 
            // Get the currently authenticated users bank
            $user = Auth::user();
            
            $bankDetail = $user->bankDetails->last();

            // Get My Bank Statement API Banks
            $bankStatementCodes = array_keys(config('bankstatement.banks'));

            // Check if bank is in my bank statment banks
            $amongBanks = in_array($bankDetail->bank_code, $bankStatementCodes);

            // the response is waiting to enable fees so we reeturn a valid response for it
            $response = $amongBanks ? ['status'=> false] : ['status'=>true];

            return response($response);

        }catch(\Exception $e) {

            return response()->json($e->getMessage(), 422);
        }
    }


    
    /**
     * Checks for when the request is not success
     *
     * @param  mixed $response
     * @return void
     */
    protected function checkUnsuccessful($response)
    {
        if ($response['status'] == '-2' || $response['status'] == '99') {
            // it was not success full
            if (is_string($response['message'])) {

                throw new \Exception($response['message']);
            }
        }
    }

    
    /**
     * userDetails
     *
     * @param  mixed $request
     * @return void
     */
    public function userDetails(Request $request)
    {
        $user = User::whereReference($request->reference)->first();

        $acceptedBanks = array_keys(config('bankstatement.banks'));

        $bankStatementEnabled = Settings::bankStatmentEnabled();
        
        $bankDetails = $user->banks->last();

        if (!isset($bankDetails)) {

            $data = ['can_request'=> false];

            return response()->json($data);
        }

        $data = [
            'phone'=> $user->phone,
            'bank'=> $bankDetails->bank_name,
            'account_number'=> $bankDetails->account_number,
            'can_request'=> in_array($bankDetails->bank_code, $acceptedBanks) && $bankStatementEnabled
        ];

        return response()->json($data);
    }

    
    /**
     * Prepares data needed to make a request
     *
     * @param  mixed $request
     * @return void
     */
    private function prepareStatementRequestData($request)
    {
        $user = User::whereReference($request->reference)->first();

        $bankDetails = $user->banks->last();

        if (! $bankDetails) {

            throw new \InvalidArgumentException('User bank details does not exists');
        }

        list($startDate, $endDate) = $this->statementService->getStatementRequestDuration();

        $applicants = [["name"=> request()->name ?? $user->name]];

        return [
            'accountNo'=> $bankDetails->account_number,
            'bankId'=> $this->statementService->getMyBankStatementBankId($bankDetails->bank_code),
            'startDate'=> $startDate,
            'destinationId'=>config('bankstatement.clientID'),
            'endDate'=> $endDate,
            'role'=> 'Applicant',
            'username'=> config('bankstatement.corporateEmail'),
            'country'=> 'NG',
            'phone'=> request()->phone ?? $user->phone,
            'applicants'=> $applicants
        ];
    }

    public function viewBankId(){
        return view('users.okra.payment');
    }

    public function getbankId(){        
        $response = $this->statementService->selectActiveRequestBanks();
        $this->checkUnsuccessful($response);
        return response()->json($response);
    }
}
