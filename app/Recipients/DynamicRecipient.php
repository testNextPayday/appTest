<?php
namespace App\Recipients;


class DynamicRecipient extends Recipient
{
    public function __construct($email)
    {
        $this->email = $email;
    }
}