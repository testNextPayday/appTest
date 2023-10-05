<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class DownloadDataController extends Controller
{
   

    public function dbDownload(Request $r)
    {

        $r->validate([
            'table' => 'required',
            'date' => 'required'
        ]);

        $data = new UserExport($r->date, $r->table);
        return Excel::download($data, $r->table. '___'.$r->date.'.xlsx');
    }
}
