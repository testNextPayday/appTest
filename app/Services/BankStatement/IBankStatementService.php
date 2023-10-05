<?php
namespace App\Services\BankStatement;


interface IBankStatementService
{ 

    public function makeStatementRequest($data);


    public  function checkFeedBackByRequestID($data);


    //public function getStatementDurations();

    public function checkFeedBackByRequestTicketNo($requestID);

    public function checkFeedBackByRequest($data);


    public function confirmStatementRequest($data);


    public function reConfirmStatementRequest($data);

   
}