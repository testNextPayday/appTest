<?php
/**
 * This class transforms Remita API responses into a response object with
 * useful getters
 * 
 */
 
 
namespace App\Remita;

use App\Unicredit\Utils\Response;
use JsonSerializable;

class RemitaResponse implements Response,JsonSerializable{
    
    public $code;
    
    public $mandateId;
    
    public $message;
    
    public $requestId;
    
    public $rawData;
    
    public $isSuccessful = false;
    
    public $hasData = false;
    
    public $responseId;
    
    public $responseDate;
    
    public $requestDate;
    
    public $data;
    
    public $rrr;
    
    public $history;
    
    public $transactionRef;
    
    public $isActive = false;
    
    public $type;
    
    public $lastStatusUpdateTime;
    
    public $authParams;
    
    public function __construct($data, $type = '')
    {
        $this->rawData = $data;
        $this->type = $type;
        
        if (!$data)
            return;
            
        $this->code = @$data->statuscode ?? @$data->responseCode;
        $this->message = @$data->responseMsg ?? @$data->status;
        $this->mandateId = @$data->mandateId;
        $this->requestId = @$data->requestId;
        
        // $this->history = @$data->history ?? [];
        $this->history = @$data->data->data->paymentDetails;
        $this->totalTransactionCount = @$data->data->data->totalTransactionCount;
        $this->totalAmount = @$data->data->data->totalAmount;
        
        $this->hasData = @$data->hasData;
        $this->responseId = @$data->responseId;
        $this->responseDate = @$data->responseDate ?? date('d-m-Y H:i:s');
        $this->requestDate = @$data->requestDate ?? date('d-m-Y H:i:s');
        $this->data = @$data->data;
        //$this->status = $data->status;
        
        $this->transactionRef = @$data->transactionRef ?? @$data->remitaTransRef;
        $this->rrr = @$data->RRR;
        
        $this->authParams = @$data->authParams;
        
        $this->endDate =  @$data->endDate;
        $this->registrationDate = @$data->registrationDate;
        $this->isActive = @$data->isActive;
        $this->activationDate = @$data->activationDate;
        $this->startDate = @$data->startDate;
        $this->lastStatusUpdateTime = @$data->lastStatusUpdateTime;
        
        $this->setSuccessStatus($type);
    }
    
    
    public function getMessage()
    {
        return $this->message;
    }
    
    public function getStatusCode()
    {
        return $this->code;
    }
    
    public function getMandateId()
    {
        return $this->mandateId;
    }
    
    public function getRequestId()
    {
        return $this->requestId;
    }
    
    public function getHistory()
    {
        return $this->history;
    }
    
    public function getRawData()
    {
        return $this->rawData;
    }
    
    public function isASuccess()
    {
        return $this->isSuccessful;       
    }
    
    private function setSuccessStatus($type)
    {
        $this->isSuccessful = in_array(
            $this->getStatusCode(), 
            $this->getSuccessCodes($type)
        );
    }
    
    private function getSuccessCodes($type)
    {
        $successCodes = [
            'mandate-setup' => ['040'],
            'mandate-stop' => ['00'],
            'mandate-status' => ['00'],
            
            'das-disburse' => ['00'],
            
            'debit-order' => ['069'],
            'debit-cancel' => ['00'],
            'debit-status' => ['00'],
            
            'collection-check' => ['00'],
            
            'mandate-authorization' => ['00'],
            'mandate-activation' => ['00']
        ];
        
        return @$successCodes[$type] ?? [];
    }
    
    public function hasData()
    {
        return $this->hasData;
    }
    
    public function getResponseId()
    {
        return $this->responseId;
    }
    
    public function getResponseDate()
    {
        return $this->responseDate;
    }
    
    public function getRequestDate()
    {
        return $this->requestDate;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function getRRR()
    {
        return $this->rrr;
    }
    
    public function getTransactionRef()
    {
        return $this->transactionRef;
    }
    
    public function isActive()
    {
        return $this->isActive;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getLastStatusUpdateTime()
    {
        return $this->lastStatusUpdateTime;
    }
    
    //get auth params for mandate activation
    public function getAuthParams()
    {
        return $this->authParams;
    }

    public function JsonSerialize()
    {
        return [
            'code'=>$this->code,
            'mandateId'=>$this->mandateId,
            'message'=>$this->message,
            'requestId'=>$this->requestId,
            'rawData'=>$this->rawData,
            'isSuccessful'=>$this->isSuccessful,
            'hasData'=>$this->hasData,
            'responseId'=>$this->responseId,
            'responseDate'=>$this->responseDate,
            'requestDate'=>$this->requestDate,
            'data'=>$this->data,
            'rrr'=>$this->rrr,
            'history'=>$this->history,
            'transactionRef'=>$this->transactionRef,
            'isActive'=>$this->isActive,
            'type'=>$this->type,
            'lastStatusUpdateTime'=>$this->lastStatusUpdateTime,
            'authParams'=>$this->authParams
        ];
    }
}
