<?php

namespace App\Traits;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use GuzzleHttp\Client;

trait Utilities
{
    public function jsonp_decode($jsonp, $assoc = false) 
    { // PHP 5.3 adds depth as third parameter to json_decode
        if(@$jsonp[0] !== '[' && @$jsonp[0] !== '{') { // we have JSONP
            $jsonp = substr($jsonp, strpos($jsonp, '('));
        }
        
        return json_decode(trim($jsonp,'();'), $assoc);
    }
    
    //TODO: Move methods below to utilities
    public function makePostRequest($url, $headers, $fields)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = $this->jsonp_decode($result);
       
        return $result;

    }


    public function curlHttpPost($url,$headers,$fields)
    {
       
        $curl = curl_init();
       
        curl_setopt_array($curl, array(

            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>$fields,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
       
        return $this->jsonp_decode($response);

    }
    
    public function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);
    
        if($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
    
        return $subject;
    }
    
    public function makeGetRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch);

        curl_close($ch);
        return $this->jsonp_decode($result);
    }

    public function getMonthsBetweenDate(Carbon $date)
    {
        foreach (CarbonPeriod::create($date, '1 month', Carbon::today()) as $month) {
            $months[$month->format('m-Y')] = $month->format('F Y');
        }
        return $months;
    }
}