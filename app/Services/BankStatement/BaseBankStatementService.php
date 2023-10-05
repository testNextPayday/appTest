<?php
namespace App\Services\BankStatement;

use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Settings;
use App\Models\BankStatementRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


/**
 *  Default abstract class for bank statement
 */

 abstract class BaseBankStatementService 
 {


    protected $baseUrl;


    protected $response;
    
    
    /**
     * Initialize the bank statement service
     *
     * @param  mixed $client
     * @return void
     */
    public function __construct(Client $client)
    {

        $this->baseUrl = $this->getBaseUrl();

        $this->client = new $client(
            [
                'base_uri' => $this->baseUrl ,
                'headers' => [
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                    'Client-ID'     => $this->getClientID(),
                    'Client-Secret'=> $this->getClientSecret(),
                    'EndPoints'=> 'https://mybankstatement.net/TP/Help',
                    'URL'=> 'https://mybankstatement.net/TP/'
                ]
            ]
        );
    }

    
    /**
     * Get Client ID 
     *
     * @return string
     */
    protected function getClientID()
    {
        return config('bankstatement.clientID');
    }
    
    
    /**
     * Get Client Secret
     *
     * @return void
     */
    protected function getClientSecret()
    {
        return config('bankstatement.clientSecret');
    }

    
    /**
     * Get Base Url
     *
     * @return void
     */
    protected function getBaseUrl()
    {
        return config('bankstatement.baseUrl');   
    }

    
    /**
     * makeHttpRequest
     *
     * @param  mixed $relativeUrl
     * @param  mixed $method
     * @param  mixed $body
     * @param  mixed $query
     * @return void
     */
    public function makeHttpRequest($relativeUrl, $method, $body, $query=false)
    {
        
        // is_null($method) ?? throw new InvalidArgumentException(
        //     " Cannot send http request with no method specified"
        // );
       
        $url = $this->baseUrl.$relativeUrl;
        

        if ($query) {

            return $this->makeHttpQueryRequest($url, $method, $body);
        }

        return $this->makeHttpNonQueryRequest($url, $method, $body);

    }

    
    /**
     * makeHttpQueryRequest
     *
     * @param  mixed $url
     * @param  mixed $method
     * @param  mixed $body
     * @return void
     */
    protected function makeHttpQueryRequest($url, $method, $body)
    {
        $this->response = $this->client->{strtolower($method)}($url,
                ["query"=>$body]
            );
        
        return $this->retrieveResponse();
    }

    
    /**
     * makeHttpNonQueryRequest
     *
     * @param  mixed $url
     * @param  mixed $method
     * @param  mixed $body
     * @return void
     */
    protected function makeHttpNonQueryRequest($url, $method, $body)
    {
        $this->response = $this->client->{strtolower($method)}(
            $url,
            ["form_params"=>$body]
        );

        return $this;
    }


      /**
     * Get the response from paystack
     *
     * @return void
     */
    protected function retrieveResponse()
    {
        return json_decode($this->response->getBody(), true);
    }
    
    /**
     * Retrieve data from response
     *
     * @return void
     */
    protected function retrieveResponseData()
    {
        return $this->retrieveResponse()['data'];
    }
    
    /**
     * Month spans for retrieveing statements
     *
     * @return void
     */
    public function getStatementRequestDuration()
    {
        $today = Carbon::today();

        $monthDuration  = Settings::bankStatmentPeriodMonths();

        $pastDate = Carbon::today()->subMonths($monthDuration);

        return [$pastDate->format('d-m-Y'), $today->format('d-m-Y')];
    }


    public function userCurrentlyRetrievedStatement($request)
    {
        $response = ['status'=> false];
        
        $user = User::whereReference($request->reference)->first();

        $currentAcc = $user->banks->last()->account_number;

        $statementRequest  = $user->lastSuccessBankStatementRequest();

        if (! isset($statementRequest)) {

            return $response;
        }

        $statementRequestAcc = $statementRequest->account_number;

        // Account number matches
        $accountMatches = $currentAcc == $statementRequestAcc;

        $now = Carbon::now();

        if ($statementRequest->created_at->diffInDays($now) <= 2 && $accountMatches){
            $response['status'] = true;
            $response['result'] = asset(Storage::url($statementRequest->request_doc));
        }

        return $response;


    }

    
    /**
     * Retrive Mu Bank Statement ID for each nanlk
     *
     * @param  mixed $bankcode
     * @return void
     */
    public function getMyBankStatementBankId($bankcode)
    {
        $bank  = config('bankstatement.banks')[$bankcode];

        return $bank['id'];
    }

    
    /**
     *  Stores the statement response in the database;
     * 
     * @return void
     */
    public function storeStatementRequest($data)
    {
        $storageData = [
            'user_id'=> $data['user_id'],
            'request_id'=> $data['result'],
            'status_message'=> $data['message'],
            'account_number'=> $data['account_number'],
            'bank_name'=> $data['bank_name'],
            'status'=> 0,
        ];

        return BankStatementRequest::create($storageData);
    }

    
    /**
     * Checks if the response is successful
     *
     * @return void
     */
    public function isSuccessful()
    {
        return $this->retrieveResponse()['status'] == '00';
    }

    
    /**
     * Updates a statement request status based on the new Request
     *
     * @param  mixed $data
     * @param  mixed $requestID
     * @return void
     */
    public function updateStatementRequestStatus($requestID, $msg, $response = [])
    {
        $bankStatement = BankStatementRequest::where('request_id', $requestID)->first();

        $updates = [];

        switch($msg) {
            case 'ticketSent':
                $updates['status'] = 1;
            break;
        }

        $bankStatement->update($updates);
    }
 }
