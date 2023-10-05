<?php
namespace Tests\utilities;

use GuzzleHttp\Psr7\Response;


class HttpTestResponseFactory

{

    protected $type;

    protected $fakeResponseFile  = '/tests/utilities/fakeResponses.php';

    public function __construct($type)
    {
        $this->type = $type;

        $this->testDatas = fileReader($this->fakeResponseFile)[$this->type];
    }


    public function createResponse($type)
    {
        
       
        if (array_key_exists($type, $this->testDatas) ) {

            $statusCode = 200;

            if (preg_match('/^fail/', $type)) {

                $statusCode = 203;
            }

            $body = $this->testDatas[$type];

           
            $response = new Response($statusCode, $headers = [], $body, "1.1");
            
            return json_decode($response->getBody(), true, 512);
        }

        $response = new Response(400, $headers = [], $body = '{}', "1.1");

        return json_decode($response->getBody(), true, 512);
       
    }

    public function createResponseArray($type,$count)
    {

        $responseArray = [];

        for ($i = 0; $i < $count; $i++) {

            $responseArray[] = $this->createResponse($type);
        }

        return $responseArray;
    }
}
?>