<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, PDF, Mail;

use App\Mail\InvestmentMade;
use App\Notifications\TestMail;
use App\Recipients\DynamicRecipient;
use App\Models\Loan;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('testMail');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    
    public function testMail()
    {
        $recipient = new DynamicRecipient('melas.devsq@olotusquare.co');
        $recipient->notify(new TestMail());   
    }
    
    public function testPDF()
    {
        $pdf_name = 'test.pdf';
        $pdf = PDF::loadView('pdfs.investment_certificate', [
            'amount' => 10000
        ]);

        $certificateLink = public_path() . '/storage/pdfs/certificates/' .$pdf_name;
        $pdf->save($certificateLink);
        Mail::to("melas@test.com")->send(new InvestmentMade($certificateLink));
    }
    
    public function testUpload(Request $request)
    {
        $request->file->store('test/uploads');
    }

    
}
