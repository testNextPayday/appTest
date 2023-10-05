<?php

namespace App\Repositories;

class Sharenet implements SmsRepository 
{
    private $url = 'http://app.sharenet.io/api/send';
    
    public function send($receiver, $sender, $message) {
        $fields = array(
            'from' => $sender, 
            'to' => '234' . substr($receiver, -10), 
            'message' => $message
        );
        
        $header = array();
        $header[] = 'Authorization: Bearer ' . config('unicredit.sharenet_token');
        $header[]= 'Accept: application/json';
            
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }    
}