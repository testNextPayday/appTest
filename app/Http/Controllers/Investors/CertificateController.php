<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;
use App\Models\Investor;

class CertificateController extends Controller
{
    //
    public function index()
    {
        $investor = request()->user('investor');

        return view('investors.certificate.index',['investor'=>$investor]);
    }




    public function pdf(Request $request)
    {
        $investor = Investor::whereReference($request->reference)->first();
        $pdf_link = $this->getInvestmentPDF($investor);
       
        return response()->file($pdf_link);
    }

    public function getInvestmentPDF($investor)
    {
        $pdf_name = 'Cert-' . $investor->reference . '.pdf';
        $loanFundAmount = $investor->loanFunds->sum('amount');
        $portfolioSize = optional($investor)->portfolioSize();
        $presentValue = $investor->presentValue();
        $backedLoans = $investor->getBackedLoans();
        $withdrawals  = $investor->getSuccessfulWithdrawals()->count();
        
        $cert_number = $investor->certificate_number;
        $pdf = PDF::loadView('pdfs.investors_cert', [
            'presentValue'=>$presentValue,
            'investor' => $investor,
            'portfolioSize'=>$portfolioSize,
            'backedLoans'=>$backedLoans,
            'withdrawals'=>$withdrawals,
            'cert_number'=>$cert_number
        ]);
        set_time_limit(200);

        $pdf_link = public_path() . '/storage/pdfs/investor_certs/' . $pdf_name;
        $pdf->save($pdf_link);

        return $pdf_link;
    }

}
