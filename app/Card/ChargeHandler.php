<?php
namespace App\Card;
use App\Models\BillingCard;

class ChargeHandler
{
    public function charge(BillingCard $card, $user,$entity)
    {
        $this->amount = ($entity->totalAmount)/3 * 100; // value in kobo
    }
}


?>