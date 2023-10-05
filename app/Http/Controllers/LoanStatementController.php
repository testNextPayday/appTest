<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use PDF;
use App\Mail\LoanStatement;
use Illuminate\Support\Facades\Mail;
use Swift_TransportException;
use App\Helpers\LoanStatementHelper;

class LoanStatementController extends Controller
{
    //
    public function __construct()
    { }

    public function get_loan_statement_pdf($loan)
    {
        $pdf_name = 'Loan-' . $loan->reference . '.pdf';
        $pdf = PDF::loadView('pdfs.loan_statement', [
            'loan' => $loan
        ]);
        set_time_limit(200);

        $pdf_link = public_path() . '/storage/pdfs/loan_statements/' . $pdf_name;
        $pdf->save($pdf_link);

        return $pdf_link;
    }

    public function view_loan_statement(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
       
        $pdf_link = $this->get_loan_statement_pdf($loan);
        return response()->file($pdf_link);

        //return view('pdfs.loan_statement',['loan'=>$loan]);

    }

    public function download_loan_statement(Request $request)
    {
        $loan = Loan::whereReference($request->reference)->first();
        $pdf_link = $this->get_loan_statement_pdf($loan);
        return response()->download($pdf_link);
    }

    public function mail_loan_statement(Request $request)
    {
      
        try {
            $loan = Loan::whereReference($request->reference)->first();
            $pdf_link = $this->get_loan_statement_pdf($loan);
            $email = $loan->user->email;
            Mail::to($email)->send(new LoanStatement($pdf_link, $loan->reference));

        } catch (\Exception | Swift_TransportException $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Mail has been sent');
    }
}
