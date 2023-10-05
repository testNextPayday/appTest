<?php

namespace App\Http\Controllers\Admin\Repayments;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Loan;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DueLoanRepaymentsExport;

class ExportController extends Controller
{
    /**
     * Exports due loans of a specific collection method
     * 
     * @param $type Type of collection method
     * 
     */
    public function exportDue()
    {
        $type = request('type');
        
        return Excel::download(
            new DueLoanRepaymentsExport($type, request('start_date'), request('end_date')), 
            "$type.xlsx"
        );
    }
}
