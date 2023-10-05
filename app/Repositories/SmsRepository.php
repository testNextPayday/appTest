<?php

namespace App\Repositories;

interface SmsRepository 
{
    
    public function send($receiver, $sender, $messgae);

   
    
}