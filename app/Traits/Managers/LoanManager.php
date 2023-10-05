<?php


namespace App\Traits\Managers;

use PDF;
use App\Models\Loan;
use App\Helpers\FinanceHandler;
use App\Models\LoanTransaction;
use App\Helpers\TransactionLogger;
use Illuminate\Support\Facades\DB;
use App\Unicredit\Utils\FulfillingService;

trait LoanManager
{
    
    public function getAuthorityForm(Loan $loan, $type = 'ippis')
    {
        $pdf = PDF::loadView('pdfs.ippis_authority', $loan->getAuthorityFormData($type));
        return $pdf->stream();
    }

    

    public function moveToFulfilled($loan)
    {
        try {
            DB::beginTransaction();

           $fulfillingService = (new FulfillingService())->fulfill($loan);
          
        }catch(\Exception $e){
            DB::rollback();
            throw new \Exception($e->getMessage());
        }

        DB::commit();
       
    }


    public function getSweepableLoans()
    {
        $loans = Loan::where('status','1')->whereHas('user.billingCards')->whereHas('repaymentPlans')->get();
        return $loans;
    }

   
    
}