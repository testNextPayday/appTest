<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;

class SkippedRepaymentExport implements FromCollection
{

    use Exportable;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        return $this->collection;
    }
}
