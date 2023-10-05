<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Structures\RestructuringObject;
use App\Http\Requests\RestructureRequest;
use App\Unicredit\Armotization\LoanArmotizer;
use App\Unicredit\Collection\CollectionService;
use App\Notifications\Users\LoanSetupNotification;
use App\Unicredit\Armotization\RestructuringLoanArmotizer;

class LoanRestructureController extends Controller
{
    //
    public function __construct()
    {
        
    }

    public function restructure(RestructureRequest $request, Loan $loan)
    {
       
        try{

            DB::beginTransaction();

            $armotizer =  new LoanArmotizer(new RestructuringLoanArmotizer);

            $data = [

                'loan'=>$loan,

                'duration'=>$request->duration,

                'charge'=>$request->charge,

                'addedAmount'=>$request->addedAmount
            ];
            
            $armotizer->armotize($data);
           
            DB::commit();

        }catch(\Exception $e){

            DB::rollback();

            return response()->json(['failure'=>$e->getMessage()], 422);
        }

        return response()->json(['success'=>'Loan was Successfully Rescheduled'], 200);
        
    }


    public function setupLoan(Request $request, Loan $loan)

    {
        
        try{

            DB::beginTransaction();

            $this->updateSetupMethod($request, $loan);

            // stop old mandates on this loans

            $collectionService = new CollectionService();

            $collectionService->prepare($loan);

            //notify the owner of the loan to setup
            $loan->user->notify(new LoanSetupNotification($loan));

            DB::commit();

        }catch(\Exception $e){

            DB::rollback();

            return response()->json(['failure'=>$e->getMessage()], 422);
        }

        return response()->json(['success'=>'Setup was successful'], 200);
        
    }



    public function updateSetupMethod($request,$loan)
    {
        $updates = [

            'collection_plan' => $request->collection_plan,

            'collection_methods' => json_encode([

                ["code" => $request->collection_plan, "status" => 0, "type" => 'primary'],

                ["code" => $request->collection_plan_secondary, "status" => 0, "type" => 'secondary'],

            ])
        ];

        // update loan
        $loan->update($updates);

    }


    
}
