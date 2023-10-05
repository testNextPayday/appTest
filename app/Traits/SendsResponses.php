<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;


trait SendsResponses

{

    protected function sendJsonErrorResponse($e, $statusCode = 422)
    {
        if ($e instanceof ClientException) {

            $err = json_decode((string)$e->getResponse()->getBody());
            $msg = $err->message;
            
        } else {

            Log::channel('unexpectederrors')->debug(
                $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine()
            );

            $msg = $e->getMessage();
        }
        

        return response()->json($msg, $statusCode);
    }


    protected function sendExceptionResponse($e)
    {

        if ($e instanceof ClientException) {

            $err = json_decode((string)$e->getResponse()->getBody());
            $msg = $err->message;
            
        } else {
            Log::channel('unexpectederrors')->debug(
                $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine()
            );
            $msg = $e->getMessage();
        }
        

        return redirect()->back()->with('failure', ' Error '.$msg);
    }

}
?>