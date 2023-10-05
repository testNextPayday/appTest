<?php
namespace App\Repositories;

interface WhatsappInterface

{
    public function whatsappNotify($phone,$message);
}
?>