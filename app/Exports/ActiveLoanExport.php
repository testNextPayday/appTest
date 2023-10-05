<?php

namespace App\Exports;

use App\Models\Employer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ActiveLoanExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     //
    // }

    public $employer_id, $from;


    public function __construct($employer_id, $from)
    {
        $this->employer_id = $employer_id;
        $this->from = $from;
    }

    // public function headings(): array
    // {
    //     # code...

    //     return $this->columns;
    // }

    public function view(): View
    {
        
        return view('admin.reports.employers', 
        // [
        //     'employers' => Employer::all()
        //     // find($this->employer_id)
        //     // ->employeeLoansQuery()
        //     // ->active()
        //     // ->whereDate('created_at', '>=', $this->from)
        //     // ->get()
        // ]
    );
    }
    
}
