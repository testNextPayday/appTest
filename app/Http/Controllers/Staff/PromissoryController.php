<?php

namespace App\Http\Controllers\Staff;


use App\Models\Investor;
use Illuminate\Http\Request;
use App\Models\PromissoryNote;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvestorBank;
use App\Models\PromissoryNoteSetting;
use App\Services\Investor\PromissoryNoteService;

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

        return view('staff.promissory-notes.new', ['investors'=> $investors, 'settings'=> $settings]);
    }

    
    /**
     * Get an Index of Note
     *
     * @return void
     */
    public function index()
    {
        $promissoryNotes = PromissoryNote::active()->get()->sortByDesc('id');

        return view('staff.promissory-notes.index', ['promissoryNotes'=> $promissoryNotes]);
    }

    
    /**
     * View Note
     *
     * @param  mixed $note
     * @return void
     */
    public function view(PromissoryNote $note)
    {
        return view('staff.promissory-notes.view', ['promissoryNote'=> $note]);
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
            'setting_id'=> 'required'
        ];

        try {

            DB::beginTransaction();

            $this->validate($request, $rules);

            if (!$settings = PromissoryNoteSetting::find($request->setting_id)) {

                throw new \InvalidArgumentException('No default settings found');
            }

            $request->request->add(
                ['rate'=> $settings->interest_rate, 'tax'=> $settings->tax_rate]
            );

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
        $investors = Investor::promissoryNote()->get();

        $investorCollections = InvestorBank::collection($investors);

        return view('staff.promissory-notes.banks', ['investors'=> $investorCollections]);
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
                'bank_code'=> 'required|max:3|string|size:3',
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
