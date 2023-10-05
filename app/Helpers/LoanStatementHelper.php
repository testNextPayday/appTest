<?php
namespace App\Helpers;
use Illuminate\Database\Eloquent\Collection;

class LoanStatementHelper
 {

    public $balance;
    public $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
        $sorted = $this->sortData();
    }

    public function sortData()
    {
        return $this->data->sortBy(function($item,$index){
           dump($item);
        });
    }
}


?>