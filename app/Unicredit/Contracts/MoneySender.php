<?php
namespace App\Unicredit\Contracts;

interface MoneySender

{
    

   public function createTransfer($data);

   public function createRecipient($data);

   public function verifyTransfer($data);

   public function finalizeTransfer($data);

   public function setHttpClient($client);

   public function ledgerHistory($data);

   public function checkBalance();

   public function disableOtp();

   public function enableOtp();

   public function finalizeDisableOtp($data);

   public function resendTransferOtp($data);

   
}
?>