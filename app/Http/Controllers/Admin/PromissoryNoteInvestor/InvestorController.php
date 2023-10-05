<?php

namespace App\Http\Controllers\Admin\PromissoryNoteInvestor;

use App\Models\BankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Investor\PromissoryInvestorService;
use App\Notifications\Investors\AccountCreated as InvestorAccountCreated;

class InvestorController extends Controller
{
    //
    protected $service;

    public function __construct(PromissoryInvestorService $service)
    {
        $this->service = $service;
    }


    
    /**
     * Creates an Investor
     *
     * @param  mixed $request
     * @return void
     */
    public function create(Request $request)
    {
        $rules = [
            'phone' => 'required|string|size:11|unique:investors',
            'email' => 'required|string|email|max:255|unique:investors',
            'name' => 'required|string|max:255'
        ];
        
        $this->validate($request, $rules);
        
        try {

            DB::beginTransaction();

            $request->request->add(
                ['is_verified'=> true, 'adder_type'=> 'App\Models\Admin', 'adder_id'=> Auth::id(), 'role'=> 2]
            );

            $investor = $this->service->createInvestor($request);

            $bank  = new BankDetail;

            $bank->code = $request->bank_code;
            $bank->account_number = $request->account_number;
            $bank->bank_name = config('remita.banks')[$request->code];

            $investor->banks()->create($bank);

            DB::commit();
    
            return redirect()->back()->with('success', 'Investor created. Please proceed to apply for investor verification');
            
        } catch (Exception $e) {

            DB::rollback();

            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
    }
}
