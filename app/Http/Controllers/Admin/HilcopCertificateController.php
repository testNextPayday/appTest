<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\HilcopCertification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Mail\InvestmentMade;

use Carbon\Carbon;
use Mail, PDF, NumberFormatter;

class HilcopCertificateController extends Controller
{
    public function HilcopCertifications()
    {
        $certificates = HilcopCertification::latest()->paginate(30);
        return view('admin.certificates.hilcop.index', compact('certificates'));
    }

    public function createHilcopCertification()
    {

        return view('admin.certificates.hilcop.new');
    }

    public function storeHilcopCertification(Request $request)
    {
        $rules = [
            'name' => 'required',
            'membership_date' => 'required|date',
            'no_of_shares' => 'required',
            'value_per_share' => 'required',
            'address' => 'required',

        ];

        $certificate = new HilcopCertification();
        $reference = $certificate->generateReference();


        $data = $request->all();
        $data['reference'] = $reference;
        $membership_date = Carbon::parse($data['membership_date']);
        $data['membership_date'] = $membership_date;
        try {

            DB::beginTransaction();
            $pdf_name = $reference . '.pdf';
            //return view('pdfs.test');
            $pdf = PDF::loadView('pdfs.hilcop_certificate', $data)->setPaper('a4', 'landscape');
            $storePath = 'pdfs/certificates/' . $pdf_name;
            $certificateLink = public_path() . '/storage/' . $storePath;
            $pdf->save($certificateLink);

            $data['certificate'] = 'public/' . $storePath;

            $certificate = HilcopCertification::create($data);
            
        } catch (\Exception $e) {
            DB::rollBack();
           
            return redirect()->back()->with('failure', 'Certificate could not be created. Please try again');
        }

        DB::commit();

        return redirect()->back()->with('success', 'Certificate created successfully');
    }



    public function deleteHilcopCertification(Request $request)
    {
        $cert = HilcopCertification::find($request->id);
        try {
            Storage::delete($cert->certificate);
            $cert->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('failure', $e->getMessage());
        }
        return redirect()->back()->with('success', 'Certificate deleted successfully');
    }
}
