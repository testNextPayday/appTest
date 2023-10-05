<?php
namespace App\Unicredit\Fakes;

use App\Facades\BaseFacade;
use App\Repositories\SmsInterface;

class FakeSms extends BaseFacade implements SmsInterface
{

    public function sendSMS($phone,$message)
    {
        return true;
    }
}
?>