<?php

namespace App\Http\Controllers\Investors;

use App\Models\Settings;
use Illuminate\Http\Request;
use App\Helpers\FinanceHandler;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Unicredit\Payments\WithdrawalHandler;

class WithdrawalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $investor = auth()->guard('investor')->user();
        $withdrawals = $investor->withdrawals()
                                ->latest()
                                ->paginate(20);
                                
        $withdrawal_limit = Settings::investorMinimumWalletBalance();

        return view('investors.withdrawals.index', compact('withdrawals','withdrawal_limit'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, FinanceHandler $financeHandler)
    {

        try{

           DB::beginTransaction();

           $user = auth('investor')->user();
           
           $array = [
            'amount'=>$request->amount
            
            ];

            $this->withdrawer = $user;

            $this->validateRequest($array);

            $withdrawData = $array;

            $withdrawal = $this->withdrawer->withdrawals()->create($withdrawData);

            $code = config('unicredit.flow')['withdrawal'];

            $financeHandler->handleDouble(

                $this->withdrawer, $this->withdrawer, $array['amount'] , $withdrawal, 'WTE', $code
            );

        //    $response = $withdrawlhandler->handleRequest($array,$user);

        //    session()->flash('info',$response['message']);

        }catch(\Exception $e){

            DB::rollback();

            return $this->sendExceptionResponse($e);
        }

        DB::commit();

        return redirect()->back()->with('success', 'Request placed successfully');

        
    }


    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function validateRequest($request)

    {
        // allows custom validation by the model involved
        if(method_exists($this->withdrawer,'validateWithdrawals')){

            return $this->withdrawer->validateWithdrawals($request);
        }

        throw new \BadMethodCallException(get_class($this->withdrawer." cannot validate withdrawals so cannot withdraw"));
       
        
    }
}
