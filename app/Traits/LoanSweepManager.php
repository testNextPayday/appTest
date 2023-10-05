<?php
namespace App\Traits;

use App\Models\Loan;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\RepaymentPlan;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Collection\CardService;
use App\Unicredit\Collection\CardServiceDecorator;



trait LoanSweepManager
{

    public function sweepLoan(Request $request, Loan $loan)

    {
        try{

            $loanSweeperService = (new CardServiceDecorator(new CardService(new Client, new DatabaseLogger)));
            $loanSweptCounter = $loanSweeperService->sweepLoan($loan);

        }catch(\Exception $e){
           
            return $this->sendExceptionResponse($e);
        }

        if($loanSweptCounter > 0){
            
            return redirect()->back()->with('success'," Successful $loanSweptCounter repayments successfully swept");
        }

        return redirect()->back()->with('info','No Repayment was successfully swept');
       
        
    }


   
}



?>