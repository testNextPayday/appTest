<?php
namespace App\Recipients;


class PhoneRecipient extends Recipient
{
    public function __construct($phone)
    {
        $this->phone = $phone;
    }
}