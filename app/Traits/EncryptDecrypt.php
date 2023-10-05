<?php

namespace App\Traits;

trait EncryptDecrypt
{
    public function basicDecrypt($encrypted_value)
    {
        try {
            $decrypted_value = decrypt($encrypted_value);
        } catch(Exception $e) {
            $decrypted_value = null;
        }
        
        return $decrypted_value;
    }
}