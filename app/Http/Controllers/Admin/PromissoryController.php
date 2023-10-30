<?php

namespace App\Http\Controllers\Admin;


use App\Models\Investor;
use Illuminate\Http\Request;
use App\Models\PromissoryNote;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvestorBank;
use App\Models\PromissoryNoteSetting;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheManager\CacheConstants;
use App\Services\Investor\PromissoryNoteService;
use Illuminate\Support\Facades\Http;

class PromissoryController extends Controller
{

    private $promissoryService;

    public function __construct(PromissoryNoteService $promissoryService)
    {
        $this->promissoryService = $promissoryService;
    }

    
    /**
     * Get a 
     *
     * @return void
     */
    public function create()
    {
        $investors = Investor::promissoryNote()->active()->get();
        $settings = PromissoryNoteSetting::all();
        return view('admin.promissory-notes.new', ['investors'=> $investors, 'settings'=>$settings]);
    }

    
    /**
     * Get an Index of Note
     *
     * @return void
     */
    public function index()
    {
        $promissoryNotes = PromissoryNote::all()->sortByDesc('id');

        return view('admin.promissory-notes.index', ['promissoryNotes'=> $promissoryNotes]);
    }


    /**
     * Get an Index of Note
     *
     * @return void
     */
    public function active()
    {
        $promissoryNotes = PromissoryNote::active()->get()->sortByDesc('id');
        $totalActive = Cache::get(CacheConstants::A_PAYDAY_NOTE_SUM);
        $totalCurrent = Cache::get(CacheConstants::A_PAYDAY_NOTE_CURRENT_SUM);
        return view('admin.promissory-notes.active', [
            'promissoryNotes'=> $promissoryNotes, 
            'totalActive'=>$totalActive,
            'totalCurrent'=>$totalCurrent
        ]);
    }


    public function pending()
    {
        $promissoryNotes = PromissoryNote::pending()->get()->sortByDesc('id');

        return view('admin.promissory-notes.pending', ['promissoryNotes'=> $promissoryNotes]);
    }

    
    /**
     * View Note
     *
     * @param  mixed $note
     * @return void
     */
    public function view(PromissoryNote $note)
    {
        return view('admin.promissory-notes.view', ['promissoryNote'=> $note]);
    }

    
        
    /**
     * Store a Note
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        
        $rules = [
            'start_date' => 'required|date',
            'amount' => 'required',
            'tenure' => 'required',
            'investor_id'=> 'required',
            'rate'=> 'required',
            'tax'=> 'required'
        ];

        try {

            DB::beginTransaction();

            $this->validate($request, $rules);
           
            if (!$investor = Investor::find($request->investor_id)) {
                throw new \InvalidArgumentException('Could not find Investor');
            }

            if ($response = $this->promissoryService->createPromissoryNote($request, $investor)) {

                DB::commit();

                return redirect()->back()->with('success', 'Promissory Note is pending approval');
            }

            return redirect()->back()->with('failure', 'An error occured. Try again');

        }catch (\Exception $e) {

            DB::rollback();
            
            
            return redirect()->back()->with('failure', $e->getMessage());
        }
        
       
    }

    
    /**
     * Investors bank records
     *
     * @param  mixed $request
     * @return void
     */
    public function investorBank(Request $request)
    {
        
        $getBanks =  Http::get('https://api.paystack.co/bank');
       $banks = json_decode($getBanks);


        $investors = Investor::promissoryNote()->get();

        $investorCollections = InvestorBank::collection($investors);

        return view('admin.promissory-notes.banks', ['investors'=> $investorCollections, 'banks'=>$banks->data]);
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

    
    /**
     * Approve a promissory note
     *
     * @param  mixed $request
     * @param  mixed $note
     * @return void
     */
    public function approve(Request $request, PromissoryNote $note)
    {
        try {

            DB::beginTransaction();

            $response = $this->promissoryService->performApprovalTaskLineUp($request, $note);
            
            DB::commit();


            return redirect()->back()->with('success', 'Promissory Note has been approved');

        }catch(\Exception $e) {

            DB::rollback();
           
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }

    
    /**
     * Update a promissory note
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\PromissoryNote $promissoryNote
     * @return void
     */
    public function update(Request $request, PromissoryNote $promissoryNote)
    {
        try {

            $data = $request->all();
            $data['amount'] = $data['principal'];
            $response = $this->promissoryService->update($promissoryNote, $data);
            
            return redirect()->back()->with('success', 'Promissory Note has been updated');

        }catch (\Exception $e) {
            
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }

    
    /**
     * Delete a promissory note
     *
     * @param  mixed $request
     * @param  mixed $promissoryNote
     * @return void
     */
    public function delete(Request $request, PromissoryNote $promissoryNote)
    {
        try {
        
            $this->promissoryService->delete($promissoryNote);
            
            return redirect()->back()->with('success', 'Promissory Note has been deleted');

        }catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }
    }



    
    /**
     * Store investors bank
     *
     * @param  mixed $request
     * @return void
     */
    public function investorBankStore(Request $request)
    {
        try {

            $this->validate($request, [
                'bank_code'=> 'required',
                'account_number'=> 'required|size:10'
            ]);

            $investor  = Investor::find($request->investor_id);

            $data = $request->only(['account_number', 'bank_code']);

            $response = $this->promissoryService->createBankDetails($investor, $data);


            if ($response) {

                return redirect()->back()->with('success', 'Bank details updated');
            }

            return redirect()->back()->with('failure', 'An error occured. Try again');

        }catch(\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }
    }

    
}
