<?php
namespace App\Traits;

/** This trait is used by an admin to control transfers */
trait TransferControls

{


    public function getBalanceHistory($payload)
    {
        $response = $this->channel->ledgerHistory($payload)['data'];

        return $response;
    }

    public function checkPaymentBalance()
    {

        $response = $this->channel->checkBalance();

        return $response;
    }


    public function disableOtpTransfers()
    {

        $response = $this->channel->disableOtp();

        return $response;
    }

    public function enableOtpTransfers()
    {
        $response = $this->channel->enableOtp();

        return $response;
    }

    public function disableOtpWithToken($data)
    {
        $response = $this->channel->finalizeDisableOtp($data);

        return $response;

    }


    



}

?>