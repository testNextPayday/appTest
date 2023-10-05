<?php
namespace App\Unicredit\Exceptions;

use Exception;
use Throwable;

class ZeroPortfolioBalanceException extends Exception
{
    public function __construct($message = '',$code=0,Throwable $previous = Null, $params = Null)
    {
        parent::__construct($message,$code,$previous);
        $this->params = $params;
    }

    public function getCustomParams()
    {
        return $this->params;
    }
}


?>