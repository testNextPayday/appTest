<?php


namespace App\Helpers;

use App\Traits\ReferenceNumberGenerator;


class FinanceHandler {
    
    use ReferenceNumberGenerator;
    
    protected $refPrefix = 'UC-WT-';
    
    protected $logger;
    
    protected $reference;
    
    protected $entity;
    
    protected $amount;
    
    protected $allowed = [
        'App\Models\Investor',  
        'App\Models\User',  
    ];
    
    public function __construct(TransactionLogger $logger)
    {
        $this->logger = $logger;
        // generate a reference for transaction
        $this->reference = $this->generateReference('App\Models\WalletTransaction');
    }
    
    public function handleDouble($from, $to, $amount, $entity, $flow = 'WTE', $code)
    {
        $this->code = $code;
        $this->entity = $entity;
        $this->flow = $flow;
        $this->amount = $amount;        
        $columns = $this->getColumns(); 
        $this->debit($from, $columns[0]);
        $this->credit($to, $columns[1]);
    }
    
    public function handleSingle($account, $type = 'credit', $amount, $entity, $flow = 'W', $code)
    {
        $this->code = $code;
        $this->entity = $entity;
        $this->flow = $flow;
        $this->amount = $amount;        
        if (!in_array($type, ['credit', 'debit'])) return;        
        $column = $this->getColumns()[0];
        $this->$type($account, $column);
    }
    
    protected function credit($account, $column)
    {
        $update = $account->update([$column => $account->$column + $this->amount]);
        $this->log($account, 1, $column);
        return $update;
    }
    
    protected function debit($account, $column)
    {
        $update = $account->update([$column => $account->$column - $this->amount]);
        $this->log($account, 2, $column);
        return $update;
    }
    
    protected function getColumns()
    {
        return explode('-', config("unicredit.asset_flow")[$this->flow]);
    }
    
    private function log($account, $direction, $purse)
    {       return $this->logger->log(
            $account, 
            $this->amount, 
            $this->code,
            $this->reference,
            $direction,
            $purse,
            $this->entity
        );
    }
}