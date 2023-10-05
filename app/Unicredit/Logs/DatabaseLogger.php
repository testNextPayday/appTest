<?php

namespace App\Unicredit\Logs;

use App\Remita\RemitaResponse;
use App\Remita\RemitaResponseCollection;
use App\Models\Log;
use App\Unicredit\Utils\Response;
use Illuminate\Support\Facades\Log as sysLog;

use Illuminate\Database\Eloquent\Model;

class DatabaseLogger implements Logger
{
    
    public function log($resource, $data)
    {
        if ($data instanceof RemitaResponse)
            return $this->logRemitaData($resource, $data);

        if($data instanceof RemitaResponseCollection)
            return $this->logRemitaResponses($resource,$data);
        
        if (is_array($data))
            return $this->logArrayData($resource, $data);
    }
    
    private function logArrayData($resource, $data)
    {
        if ($resource instanceof Model) {
            $data['resource_type'] = get_class($resource);
            $data['resource_id'] = $resource->id;
        }   
        $data['auto_generated'] = php_sapi_name() === 'cli';
        
        return Log::create($data);
    }

    private function logRemitaResponses($resource,RemitaResponseCollection $data)
    {

            $logData = [];
            if($resource instanceof Model){
                $logData['resource_type'] = get_class($resource);
                $logData['resource_id'] = $resource->id;
            }
            // tofdo implement remitta collection responses
            $logData['title'] = ucwords(str_replace('-', ' ', $data->getType()));
            $description = $this->buildDescription($resource, $data);
            $logData['description'] = $description;
            $logData['message'] = $description;
            $logData['status'] = $data->isASuccess();
           
            // check if its cron
            $logData['data_dump'] = json_encode((array) $data->getRawData());
            $logData['auto_generated'] = php_sapi_name() === 'cli';
            
            return Log::create($logData);

    }
    
    private function logRemitaData($resource, RemitaResponse $data)
    {
        $logData = [];
        
        if ($resource instanceof Model) {
            $logData['resource_type'] = get_class($resource);
            $logData['resource_id'] = $resource->id;
        }
       
        $logData['title'] = ucwords(str_replace('-', ' ', $data->getType()));
        $description = $this->buildDescription($resource, $data);
        $logData['description'] = $description;
        $logData['message'] = $description;
        $logData['status'] = $data->isASuccess();
        
        // check if its cron
        $logData['data_dump'] = json_encode((array) $data->getRawData());
        $logData['auto_generated'] = php_sapi_name() === 'cli';
        
        return Log::create($logData);
        
    }
    
    private function buildDescription($resource,Response $data)
    {
        switch($data->getType()) {
            case 'mandate-setup':
                $desc = 
                    "Mandate setup attempt for Loan: " . $resource->reference. ". Status: ". 
                    $data->getStatusCode() . ". Message: " . $data->getMessage();
                break;
            case 'das-disburse':
                $desc =
                    "DAS Disbursement for Loan: " . $resource->reference . ". Status: " .
                    $data->getStatusCode() . ". Message: " . $data->getMessage();
                break;
            case 'debit-order':
                $desc =
                    "Debit Order Instruction for " . $resource->loan->reference . ". Status: " .
                    $data->getStatusCode() . ". Message: " . $data->getMessage();
                break;
            case 'debit-cancel':
                $desc =
                    "Debit Order Instruction Cancellation request for " . $resource->loan->reference . ". Status: " .
                    $data->getStatusCode() . ". Message: " . $data->getMessage();
                break;
            case 'collection-check':
                $desc = 
                    "Collections check for Loan " . $resource->reference . ". Current count: " . count($data->getHistory());
                break;
            case 'mandate-status':
                $status = $data->isActive() ? 'Activated' : 'Inactive';
                $desc = 
                    "Mandate Status check for Loan $resource->reference. Mandate Status: $status";
                break;
            case 'mandate-stop':
               
                $desc = " Mandate stop for loan $resource->reference. Status:". $data->getStatusCode();
                break;
            case 'mandate-authorization':
                $desc = " Mandate Request Authorization for $resource->reference. Status:".$data->getStatusCode();
                break;
            case 'mandate-activation':
                $desc = " Mandate Activation for $resource->reference. Status:".$data->getStatusCode();
                break;
            default:
                $desc = '';
        }
        
        return $desc;
    }
}