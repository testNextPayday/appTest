<?php

namespace App\Http\Controllers\Admin\Repayments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LoanRepaymentsImport;
use App\Exports\SkippedRepaymentExport;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use App\Services\ExcelRepaymentUploadService;

class ImportController extends Controller
{
    public function importRepayments(Request $request)
    {
       
       
        //$this->validate($request, ['repayments' => 'required|mimetypes:xlsx,xls,csv,application/vnd.ms-excel']);
        $file = $request->file('repayments');
        if(!$file)  return redirect()->back()->with('failure',' Enter a file please');
        $extensions = array('xlsx','xls','csv');
        if(!in_array($file->getClientOriginalExtension(),$extensions)){
            $err_msg = 'File format must be of type '.implode(',',$extensions);
            return redirect()->back()->with('failure',$err_msg);
        }

       
        try{
            DB::beginTransaction();
           
             Excel::import(new LoanRepaymentsImport($request->type, $request->employer_id), $file);
           
            // retrieve the upload service from the service container
            $uploadService = App::make(ExcelRepaymentUploadService::class);

            $skips = $uploadService->getSkips();

            $uploadStatus = $uploadService->getUploadStatus();

        }catch(\Exception | QueryException $e){
            DB::rollBack();
            
            $err_msg =  isset($e->errorInfo[2]) ? $e->errorInfo[2] : $e->getMessage();
            return redirect()->back()->with('failure',$err_msg);
        }
    
       
        DB::commit();
        return redirect()->back()->with('success', $uploadStatus);
    }


    public function download()
    {
        return Storage::disk('public')->download('skippedRepayments.xls');
    }

   

}
