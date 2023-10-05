<?php

namespace App\Http\Controllers\Investors;

use App\Models\PromissoryNote;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Investor\PromissoryNoteService;
use App\Services\MonoStatement\BaseMonoStatementService;

class PromissoryController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('unverified.investor');
    }
    /**
     * All investor Promissory Notes
     *
     * @return void
     */
    public function index()
    {
        $investor = auth()->guard('investor')->user();
        $promisorryNotes = $investor->promissoryNotes;

        return view('investors.promissory-notes.index', ['promissoryNotes'=> $promisorryNotes]);
    }

    
    /**
     * Promissory Notes that are active
     *
     * @return void
     */
    public function active()
    {
        $investor = auth()->guard('investor')->user();
        $promisorryNotes = $investor->promissoryNotes()->active()->get();

        return view('investors.promissory-notes.active', ['promissoryNotes'=> $promisorryNotes]);
    }


    public function fundAccount(){
        return view('investors.promissory-notes.fund-wallet');
    }

    public function monoDirectPay(){
        $my_array = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $my_arrays = str_shuffle($my_array);
        $my_arrayz = str_shuffle($my_array);
        $reference = rand(0000,9000).substr($my_arrays,25).rand(0,9).rand(1000,9000).substr($my_arrayz,25);

        $amount = 20000;
        //initiating mono payment
        $this->monostatementService->investorFund($amount, $reference);
        $monoInfo = $this->monostatementService->getResponse();
        if($monoInfo){
            $reference = $monoInfo['reference'];
            $paymentLink = $monoInfo['payment_link'];

            return response()->json(['status' => 1, 'paymentLink' => $paymentLink], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Payment Failed'], 200);
        
    }
    
    /**
     * View a promissory note
     *
     * @param  \App\Models\PromissoryNote $promisorryNote
     * @return void
     */
    public function view(PromissoryNote $promisorryNote)
    {
        return view('investors.promissory-notes.view', ['promissoryNote'=> $promisorryNote]);
    }

    
    /**
     * Liquidates the loan
     *
     * @param  mixed $promisorryNote
     * @return void
     */
    public function liquidate(PromissoryNote $promissoryNote)
    {
        try{
            
            DB::beginTransaction();

            $promissoryNote->service->liquidate();

            DB::commit();

        }catch(\Exception $e) {

            DB::rollback();

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'The note was successfully liquidated');
    }
    
    /**
     * Rollover Note
     *
     * @param  mixed $promisorryNote
     * @param  mixed $service
     * @return void
     */
    public function rollover(PromissoryNote $promissoryNote)
    {
        try{

            DB::beginTransaction();

            $promissoryNote->service->rollover();

            DB::commit();

        }catch(\Exception $e) {

            DB::rollback();

            //dd($e->getLine().' '. $e->getFile());
            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'The note was successfully rolled over');
    }


    
    /**
     * withdraw
     *
     * @param  mixed $promisorryNote
     * @param  mixed $service
     * @return void
     */
    public function withdraw(PromissoryNote $promissoryNote)
    {
        try{
            DB::beginTransaction();

            $promissoryNote->service->withdraw();

            DB::commit();

        }catch(\Exception $e) {

            DB::rollback();
            
            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'The note was successfully created withdrawal');
    }
}
