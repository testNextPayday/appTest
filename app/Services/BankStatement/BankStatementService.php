<?php
namespace App\Services\BankStatement;

use App\Services\BankStatement\IBankStatementService;
use App\Services\BankStatement\BaseBankStatementService;

class BankStatementService extends BaseBankStatementService implements IBankStatementService
{
    
    /**
     * Initiate a bank statement call to users bank
     *
     * @param  mixed $data
     * @return void
     */
    public function makeStatementRequest($data)
    {
        // dd(json_encode($data));
        $this->makeHttpRequest('/RequestStatement', 'POST', $data);

        // After making this request we need to store the request ID to be returned
        $response = $this->retrieveResponse();

        return $response;
        
    }

    
    /**
     * Checks the status of the statement request made earlier
     *
     * @param  mixed $requestID
     * @return void
     */
    public function checkFeedBackByRequestID($data)
    {
       
        validateFields($data, 'requestId');
        $this->makeHttpRequest('/GetFeedbackByRequestID', 'POST', $data);
        // check request status for Ticket or TicketSent and update accordingly
        $response = $this->retrieveResponse();
        
        return $response;
       
        
    }


    public function selectActiveRequestBanks()
    {
        $this->makeHttpRequest('/SelectActiveRequestBanks', 'POST', []);
        $response = $this->retrieveResponse();
        return $response;
    }

     /**
     * Checks the status of the statement using ticket
     *
     * @param  mixed $requestID
     * @return void
     */
    public function checkFeedBackByRequestTicketNo($data)
    {
        validateFields($data, 'ticketNo');
        $this->makeHttpRequest('/GetFeedbackByTicketNo', 'POST', $data);
        $response = $this->retrieveResponse();
        return $response;
        // check request status for Ticket or TicketSent
    }

     /**
     * Checks the status of the statement request made earlier
     *
     * @param  mixed $requestID or $ticketID
     * @return void
     */
    public function checkFeedBackByRequest($data)
    {
        validateFields($data, 'ID');
        $this->makeHttpRequest('/GetFeedback', 'POST', $data);
        $response = $this->retrieveResponse();
        return $response;
        // check request status for Ticket or TicketSent
    }

    
    /**
     * Prompt customer to confirm the request
     *
     * @return void
     */
    public function confirmStatementRequest($data)
    {
        validateFields($data, 'ticketNo', 'password');
        $this->makeHttpRequest('/ConfirmStatement', 'POST', $data);

        $response = $this->retrieveResponse();
        return $response;
        
    }

    
    /**
     * Prompt a reconfirm of the request 
     *
     * @param  mixed $data
     * @return void
     */
    public function reConfirmStatementRequest($data)
    {
        validateFields($data, 'requestID');
        $this->makeHttpRequest('/ReconfirmStatement', 'POST', $data);
        $response = $this->retrieveResponse();
        return $response;
    }

    
    /**
     * Get Statement as password protected pdf
     *
     * @param  mixed $data
     * @return void
     */
    public function getPDFStatement($data) 
    {
        validateFields($data, 'ticketNo');
        $this->makeHttpRequest('/GetPDFStatement', 'POST', $data);
        $response = $this->retrieveResponse();
        return $response;
    }


    /**
     * Get CVS Statement
     *
     * @param  mixed $data
     * @return void
     */
    public function getCSVStatement($data) 
    {
        validateFields($data, 'ticketNo');
        $this->makeHttpRequest('/GetCSVStatement', 'POST', $data);
        $response = $this->retrieveResponse();
        return $response;
    }

    
    /**
     * Get JSON Statement
     *
     * @param  mixed $data
     * @return void
     */
    public function getJSONStatement($data)
    {
        validateFields($data, 'ticketNo');
        $this->makeHttpRequest('/GetJSONStatement', 'POST', $data);
        $response = $this->retrieveResponse();
        return $response;
    }

     /**
     * Get JSON Object Statement
     *
     * @param  mixed $data
     * @return void
     */
    public function getJSONObjStatement($data)
    {
        validateFields($data, 'ticketNo');
        $this->makeHttpRequest('/GetStatementJSONObject', 'POST', $data);
        $response = $this->retrieveResponse();
        return $response;
    }


}