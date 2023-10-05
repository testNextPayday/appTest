<?php
namespace App\Unicredit\Fakes;

use App\Services\BankStatement\IBankStatementService;
use App\Services\BankStatement\BaseBankStatementService;

class FakeBankStatement  extends BaseBankStatementService implements IBankStatementService
{



    // public function makeHttpRequest($url, $method, $data, $query=false)
    // {
    //     switch($url) {

    //         case '/RequestStatement':
    //             return $this->handleStatementRequest($data);
    //         break;

    //         case '/GetFeedbackByRequestID':
    //             return $this->handleCheckFeedBackByRequestID($data);
    //         break;

    //         case '/GetFeedbackByRequestTicketNo':
    //             return $this->handleCheckFeedBackByRequestTicketNo($data);
    //         break;

    //         case '/GetFeedback' :
    //             return $this->handleCheckFeedBackByRequest($data);
    //         break;

    //         case '/ConfirmStatement':
    //             return $this->handleConfirmStatementRequest($data);
    //         break;

    //         case '/ReconfirmStatement':
    //             return $this->handleReConfirmStatementRequest($data);
    //         break;
    //     }
    // }
    
    public function makeStatementRequest($data)
    {
        return [
            'status'=> '00',
            'message'=> 'Successful',
            'result'=> (string)mt_rand(1000,9999)
        ];
    }


    public function checkFeedBackByRequestID($data)
    {
        return [
            'status'=> '00',
            'message'=> 'Successful',
            'result'=> [
                'status'=> 'Ticket',
                'feedback'=> 'Your ticket has been confirmed and we are working on getting your statement to the destination.'
            ]
        ];
    
    }

    public function checkFeedBackByRequestTicketNo($requestID)
    {
        return [
            'status'=> '00',
            'message'=> 'Successful',
            'result'=> [
                'status'=> 'Confirm',
                'feedback'=> 'Your ticket has been confirmed and we are working on getting your statement to the destination.'
            ]
        ];
       
    }

    public function checkFeedBackByRequest($data)
    {
        return [
            'status'=> '00',
            'message'=> 'Successful',
            'result'=> [
                'status'=> 'Confirm',
                'feedback'=> 'Your ticket has been confirmed and we are working on getting your statement to the destination.'
            ]
        ];
    }


    public function confirmStatementRequest($data)
    {
        return [
            'status'=> '00',
            'message'=> 'Successfully confirmed request'
        ];
    }


    public function reConfirmStatementRequest($data)
    {
        return [
            'status'=> '00',
            'message'=> 'Successfully confirmed request'
        ];
    }


    public function isSuccessful()
    {
        return true;
    }


    public function getPDFStatement() 
    {
        return [
            'status'=> '00',
            'message'=> 'Successful',
            'result'=> base64_encode(file_get_contents(public_path('testpdf.pdf')))
        ];
    }

    
}