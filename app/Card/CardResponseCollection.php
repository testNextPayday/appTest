<?php
namespace App\Card;
use Illuminate\Support\Collection;
use App\Card\CardResponse;

class CardResponseCollection extends Collection

{
    public function allSuccessful()
    {

        $totalItems = $this->count();
      
        $totalSuccess = $this->where('status',true)->count();
     
        return $totalSuccess == $totalItems ? true : false;
    }

    /**
     * getStatus
     *  Get the status of all responses
     * @return array
     */
    public function getStatus()
    {
        return $this->pluck('status');
    }


    public function getMessages()

    {
        return $this->buildMessage();
    }

    private function buildMessage()
    {
        $totalItems = $this->count();
        $totalSuccess = $this->where('status',true)->count();
        return "$totalSuccess Charge Success";
    }

    public function getDumps()
    {
        return $this->pluck('dump');
    }

   
}


?>