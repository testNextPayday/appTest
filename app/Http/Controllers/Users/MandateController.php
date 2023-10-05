<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


use App\Models\Loan;
use PDF, NumberFormatter, Storage;
use Carbon\Carbon;


class MandateController extends Controller
{
    public function getAuthorityForm(Loan $loan, $type = "ippis")
    {
        $pdf = PDF::loadView('pdfs.ippis_authority', $loan->getAuthorityFormData($type));
        return $pdf->stream();
    }
    
    public function uploadAuthorityForm(Request $request, Loan $loan)
    {

        $this->validate($request, [
            'authority_form' => 'required|file|mimes:pdf,jpg,png,jpeg|max:2048' 
         ]);
         
        
        try {

            $collectionDocuments = json_decode($loan->collectionDocuments) ?? [];
        
            if (@$collectionDocuments['authority_form']) {
                Storage::delete($collectionDocuments['authority_form']);    
            }
            
            $collectionDocuments['authority_form'] =
                $request->authority_form->store('public/mandates');
            
            $loan->update(['collection_documents' => json_encode($collectionDocuments)]);


            if ($request->wantsJson()) {

                return response()->json(
                    'Authority form uploaded successfully. You\'ll need to wait for Admin to evalute your submission'
                );
            }

        }catch(\Exception $e) {

            if ($request->wantsJson()) {
                
                return response()->json($e->getMessage(), 422);
            }
            return redirect()->back()->with(
                'failure', 
                $e->getMessage()
            );
        }
       
        
        return redirect()->back()->with(
            'success', 
            'Authority form uploaded successfully. You\'ll need to wait for Admin to evalute your submission'
        );
    }
}
