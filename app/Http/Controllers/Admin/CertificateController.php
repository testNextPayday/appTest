<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Mail\InvestmentMade;

use Illuminate\Http\Request;

use Mail, PDF, NumberFormatter;
use App\Http\Controllers\Controller;
use App\Models\InvestmentCertificate;
use Illuminate\Support\Facades\Storage;
use App\Events\PromissoryNoteCreatedEvent;

class CertificateController extends Controller
{
    public function investmentCertificates()
    {
        $certificates = InvestmentCertificate::active()->latest()->paginate(30);
        return view('admin.certificates.investments.index', compact('certificates'));
    }

    public function archivedCertificates()
    {
        $certificates = InvestmentCertificate::matured()->orderBy('maturity_date','desc')->paginate(30);
        return view('admin.certificates.investments.archived', compact('certificates'));
    }
    
    public function createInvestmentCertificate()
    {
        return view('admin.certificates.investments.new');
    }


        
    /**
     * storeInvestmentCertificate
     *
     * @param  mixed $request
     * @return void
     * @deprecated When promissory investment was just certificates
     */
    public function storeInvestmentCertificate(Request $request)
    {
        $rules = [
            'name' => 'required',
            'start_date' => 'required|date',
            'amount' => 'required',
            'rate' => 'required',
            'interest_payment_cycle' => 'required',
            'tenure' => 'required'
        ];

        try {

            $certificate = new InvestmentCertificate();
            $reference = $certificate->generateReference();
            
            
            $data = $request->all();

            // Unset Afiliate
            unset($data['receiverType']);
            unset($data['assignedPersonId']);

            $data['reference'] = $reference;
            $start_date = Carbon::parse($data['start_date']);
            
            $data['start_date'] = $start_date;
            $maturity_date = Carbon::parse($data['start_date'])->addMonths($request->tenure);
            $data['maturity_date'] = $maturity_date;
            
            $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
            $data['amount_in_words'] = $f->format($request->amount);
            $data['maturity_value'] = $request->amount + ((($request->amount * ($request->rate/100)) / 12) * $request->tenure );
    
            $data['tax_amount'] = ($request->tax/100) * ((($request->amount * ($request->rate/100)) / 12) * $request->tenure );
            $data['payable_value'] = $data['maturity_value'] - $data['tax_amount'];
            $pdf_name = $reference . '.pdf';
            $pdf = PDF::loadView('pdfs.investment_certificate', $data);
            $storePath = 'pdfs/certificates/' .$pdf_name;
            $certificateLink = public_path() . '/storage/' . $storePath;
            $pdf->save($certificateLink);
            
            $data['certificate'] = 'public/' . $storePath;
            
            
            $data['start_date'] = $start_date->toDateString();
            $data['maturity_date'] = $maturity_date->toDateString();
            unset($data['amount_in_words']);
            unset($data['maturity_value']);
            unset($data['tenure']);
            unset($data['tax_amount']);
            unset($data['payable_value']);
            
            if($certificate = InvestmentCertificate::create($data)) {
                if ($request->email) {
                    Mail::to($request->email)->send(new InvestmentMade($certificateLink));   
                }

                if (isset($request->receiverType)) {
                    $data = $request->only(['receiverType', 'assignedPersonId', 'tenure', 'amount']);
                    $data['note'] = $certificate;
                    event(new PromissoryNoteCreatedEvent($data));
                }

               
                return redirect()->back()->with('success', 'Certificate created successfully');
            }
            

        }catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }
        
       
    }


    public function deleteInvestmentCertificate(Request $request)
    {
        $cert = InvestmentCertificate::find($request->id);
        try{
            Storage::delete($cert->certificate);
            
            $cert->delete();
        }catch(\Exception $e){
            return redirect()->back()->with('failure', $e->getMessage());
        }
        return redirect()->back()->with('success', 'Certificate deleted successfully');

    }
}
