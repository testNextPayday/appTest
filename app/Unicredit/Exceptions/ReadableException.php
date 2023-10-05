<?php
namespace App\Unicredit\Exceptions;

use Illuminate\Database\QueryException;
use GuzzleHttp\Exception\ClientException;


/**
 * This class provides a readable interface for exceptons of all types
 */
class ReadableException extends \Exception 
{
        
    /**
     * The exception to be read
     *
     * @var \Exception 
     */
    protected $e;

    public function __construct(\Exception $exception)
    {
        $this->e = $exception;
    }

    
    /**
     * Gets the error mesage string
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $message = 'No message found';
        
        if ( $this->e instanceof ClientException ) {

            $err = json_decode((string)$this->e->getResponse()->getBody());
            $message = $err->message;
        }

        elseif ($this->e instanceof QueryException) {

            $message = $this->e->errorInfo[2];
        }

        else {

            $message = $this->e->getMessage();
        }

        return $message;
    }

}