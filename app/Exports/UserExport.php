<?php

namespace App\Exports;

use App\Models\Investor;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UserExport implements FromView, WithHeadings, ShouldAutoSize
{


    private $columns = ['name', 'email', 'created_at'];

    private $startDate;
    private $table;

    public function __construct($date, $table)
    {
        $this->startDate = $date;
        $this->table = $table;
    }

    // public function autosize(): autosize
    // {
    //     return $this->columns;
    //     // return true;
    // }


    public function headings(): array
    {
        # code...

        return $this->columns;
    }

    public function view(): View
    {
        if($this->table == 'borrowers')
            return view('admin.db_data_download', [
                'users' => User::all()
                ->where('created_at', '>=', $this->startDate)
            ]);

            if($this->table == 'investors')
            return view('admin.db_data_download', [
                'users' => Investor::all()
                ->where('created_at', '>=', $this->startDate)
            ]);
        }
}