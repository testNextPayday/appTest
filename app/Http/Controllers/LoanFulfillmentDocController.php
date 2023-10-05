<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use PDF;
use Illuminate\Http\Request;

class LoanFulfillmentDocController extends Controller
{
        
    /**
     * Preview Loan Fulfillment Doc
     *
     * @param  mixed $request
     * @param  mixed $loan
     * @return void
     */
    public function previewDoc(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
        $pdf_name = 'Loan-' . $loan->reference . '.pdf';
        $pdf = PDF::loadView('pdfs.fulfillment_preview', [
            'loan' => $loan
        ]);
        set_time_limit(200);

        $pdf_link = public_path() . '/storage/pdfs/loan_fulfillments/' . $pdf_name;
        $pdf->save($pdf_link);
        return response()->file($pdf_link);
    }
}
