<?php

namespace App\Helpers;

use App\Models\WalletTransaction;

class TransactionLogger
{
    protected $code;
    
    protected $codeData;
    
    public function log($account, $amount, $code, $reference, $direction, $purse = 'wallet', $entity = null)
    {
        $this->code = $code;
        $this->codeData = $this->getCodeData();
        $this->entity = $entity;

        $data = [
            'owner_id' => $account->id,
            'owner_type' => get_class($account),
            'amount' => $amount,
            'reference' => $reference,
            'code' => $code,
            'direction' => $direction,
            'description' => $this->getDescription(),
            'entity_id' => is_object($entity) ? $entity->id : null,
            'entity_type' => is_object($entity) ? get_class($entity) : null,
            'parties' => $this->getParties(),
            'purse' => $purse === 'wallet' ? 1 : 2
        ];   
    
        return $this->save($data);
    }
    
    private function getDescription()
    {
        $addendum = is_object($this->entity) && $this->entity->reference ? " for " . $this->entity->reference : '';
        return $this->codeData[0] .    $addendum;
    }
    
    private function getParties()
    {
        return $this->codeData[1];
    }
    
    protected function getCodeData()
    {
        return config('unicredit.flow_codes')[$this->code];
    }
    
    private function save($data)
    {
        WalletTransaction::create($data);
    }
}