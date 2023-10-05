<?php
namespace App\Traits;

use App\Models\GatewayTransaction;

trait HasGatewayRecords
{

    public function gatewayRecords()
    {
        return $this->morphMany(GatewayTransaction::class,"link");
    }

    
}

?>