<?php
namespace App\Traits\Managers;

use App\Helpers\FinanceHandler;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;
use App\Unicredit\Payments\WithdrawalHandler;


trait WithdrawalManager 
{

     
    public function approve($request, WithdrawalHandler $withdrawalHandler)
    {
        try {
            DB::beginTransaction();

        
            if (!$request) {
                throw new \InvalidArgumentException("Request not found");
            }
           
            $response = $withdrawalHandler->handleRequest($request);

            session()->flash('info', $response['message']);
           

        } catch(\Exception $e) {

            DB::rollback();

            return $this->sendExceptionResponse($e);
        }

        DB::commit();

        return redirect()->back()->with('success', 'Withdrawal Request Approved');
        
    }

    public function approveBackend($request, WithdrawalHandler $withdrawalHandler)
    {

        try {
            DB::beginTransaction();

           
            if (!$request) {
                throw new \InvalidArgumentException("Request not found");
            }

            // $withdrawalRequest->update(['status'=>'2']);
            $response = $withdrawalHandler->handleRequestBackend($request);

            //session()->flash('info', $response['message']);
           

        } catch(\Exception $e) {

            DB::rollback();

            return $this->sendExceptionResponse($e);
        }

        DB::commit();

        return redirect()->back()->with('success', 'Withdrawal Request Approved');
        
    }
    
    public function decline($request, FinanceHandler $financeHandler)
    {
       
        if(!$request) 
            return redirect()->back()->with('failure', 'Withdrawal Request not found');
        if($request->update(['status' => "4"])) {
            if ($requester = $request->requester) {
                
                $code = config('unicredit.flow')['withdrawal_rvsl'];
                $financeHandler->handleDouble(
                    $requester, $requester, $request->amount, $request, 'ETW', $code
                );
                
            }
            return redirect()->back()->with('success', 'Withdrawal Request Declined.');
        }
        return redirect()->back()->with('failure', 'An error occurred. Please try again');
    }

}