<?php
namespace App\Unicredit\Contracts;


interface PaymentGateway

{
    
    /**
     * GetAuthorizationUrl
     *  Gets the payment gateway URL
     * @return void
     */
    public function getAuthorizationUrl();
    
    /**
     * redirectNow
     *  Redirects to payment gateway
     * @return void
     */
    public function redirectNow();
    
    /**
     * getPaymentData
     * Retrieves actual data from payment made
     * @return void
     */
    public function getPaymentData();
}

?>