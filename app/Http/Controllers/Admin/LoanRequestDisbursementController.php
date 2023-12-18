<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;

use DB, Notification;
use App\Helpers\Constants;
use App\Models\LoanRequest;
use App\Models\MonoPayment;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Remita\RemitaResponse;
use App\Helpers\FinanceHandler;
use App\Traits\SettleAffiliates;
use App\Events\LoanDisbursedEvent;

use App\Remita\DDM\MandateManager;
use App\Http\Controllers\Controller;

use App\Services\LoanDisburseService;
use App\Unicredit\Logs\DatabaseLogger;
use GuzzleHttp\Exception\ClientException;
use App\Unicredit\Collection\RepaymentManager;

use App\Unicredit\Collection\CollectionService;
use App\Notifications\Users\LoanSetupNotification;
use App\Notifications\Investors\FundsDisbursedNotification;
use App\Services\MonoStatement\BaseMonoStatementService;

class LoanRequestDisbursementController extends Controller
{
    use SettleAffiliates;

    
    /**
     * @var $collectionService
     */
    private $collectionService;

    private $dbLogger;


    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }


    /**
     * prepares the loan for a loan request
     * 
     * @params Request $request, LoanRequest $loanRequest
     * 
     */
    public function prepareLoan(Request $request, LoanRequest $loanRequest)
    {
        // dd('yolo');
        // Set up a loan based on a loan request
        // for the loan, sets up sweep dates (start and end) [dd-mm]
        // sets up peak period start and end [dd-mm]
        // sets up no of debits
        // modifies EMI if necessary otherwise uses default emi calculation
        // sets sweep frequency
        // sets sweep frequency for peak periods
        // chooses collection mode - DDM, DAS, Others (Ignore others for now)
        // NOTE: DDM Special case - customer has to activate mandate

        $this->validate($request, $this->getPreparationRules());

        try {
            
            //$monoservice = new BaseMonoStatementService((new Client));

            DB::beginTransaction();
            // this variable controls if the redirectback route would work
            $routeExists = true;

            // if the request has a loan already then 
                     
            if ($loanRequest->loan) {
                throw new \Exception('The loan Request already has a loan');
            }
            
            $loan = Loan::create($this->generateLoanData($request, $loanRequest));  
            
            if($loan->upfront_interest){
                $loan->loanRequest->investorUpfrontInterest->update(
                    [
                        'loan_id'=>$loan->id
                    ]
                );
            }
            
            //Collections thingy
            $startDate = $request->startDate;
            $endDate = $request->endDate;            
            if ($startDate && $endDate) {
                $this->collectionService->setDates($startDate, $endDate);
            }
            
            $response = $this->collectionService->prepare($loan);
            

            if (is_array($response) && !$response['status'])
                throw new \Exception($response['message']);
            
            if ($response instanceof RemitaResponse && !$response->isASuccess())
                throw new \Exception($response->getMessage());

            //Update request as loan created
            $loanRequest->update(['status' => 4]);

            // send user link to setup loan
            $loan->user->notify(new LoanSetupNotification($loan));

            DB::commit();

            if ($routeExists) {

                return redirect()->back()
                    ->with('success', @$response['message'] ?? 'Loan created successfully');
            }

            
            return redirect()->route('admin.loans.view', ['reference'=>$loan->reference])
                ->with('success', @$response['message'] ?? 'Loan created successfully');

           

        } catch (\Exception $e) {
           
            // Dont rollback if the env is testing
            // This is because Remita fails with test params.
            // Remove if condition when fix is found
            if (config('app.env') !== "testing")
                DB::rollback();

            // dd($e->getMessage());
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
    }



    public function retryPreparation(Loan $loan, $code)
    {
        try {

            //Collections thingy
            $response = $this->collectionService->prepare($loan, $code);

            if (is_array($response) && !$response['status'])
                throw new \Exception($response['message']);

            if ($response instanceof RemitaResponse && !$response->isASuccess())
                throw new \Exception($response->getMessage());

            // send user link to setup loan
           $loan->user->notify(new LoanSetupNotification($loan));

            return redirect()->back()
                ->with('success', @$response['message'] ?? 'Collection method setup successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('failure', 'Error: ' . $e->getMessage());
        }
    }


    /**
     * Disburses a prepared loan
     * 
     * @params Loan $loan
     */
    public function disburseLoan(Loan $loan, LoanDisburseService $disburseService)
    {
        try {

            DB::beginTransaction();

            $disburseService->disburseLoan($loan);

            DB::commit();

            return redirect()->back()->with(
                'success', 
                'Loan disbursed successfully'
            );

        } catch (\Exception $e) {

            DB::rollback();
            // dump($e->getMessage());
            // dump($e->getTraceAsString());
            return $this->sendExceptionResponse($e);
        }
    }


    /**
     * Disburses a loan from backend
     * 
     * @params Loan $loan
     */
    public function disburseLoanBackend(Loan $loan, LoanDisburseService $disburseService)
    {

            try {

                DB::beginTransaction();
    
                $disburseService->disburseFromBackend($loan);
    
                DB::commit();
    
                return redirect()->back()->with(
                    'success', 
                    'Loan disbursed successfully'
                );
    
            } catch (\Exception $e) {
    
                DB::rollback();
                // dump($e->getMessage());
                // dump($e->getTraceAsString());
                return $this->sendExceptionResponse($e);
            }
        
    }


    /**
     * Checks the status of a DDM Mandate
     * 
     *  @params Loan $loan, MandateManager $manager
     */
    public function checkMandateStatus(Loan $loan)
    {
        $response = $this->collectionService->checkMandateStatus($loan);

      
        if ($response->isActive()) {
            // mandate is active and loan is ready for disbursement
            $loan->updateCollectionMethodStatus(Constants::DDM_REMITA, 2);

            return redirect()->back()
                ->with('success', 'Mandate is active and loan is now ready for disbursement');
        }

        return redirect()->back()->with('info', 'Mandate has not been activated');
    }


    /**
     * Returns the rules for preparing a loan
     * 
     * @return array
     */
    private function getPreparationRules()
    {
        return [

            'collection_plan' => 'required|numeric',
            'emi' => 'required'

        ];
    }


    /**
     *  Generates loan data for a loan request
     * 
     *  @params Request $request, LoanRequest $loanRequest
     * 
     *  @return array
     */
    private function generateLoanData(Request $request, LoanRequest $loanRequest)
    {
       
        $data =  [
            'request_id' => $loanRequest->id,
            'user_id' => $loanRequest->user_id,
            'amount' => $loanRequest->amountRealized,
            'due_date' => now()->addMonths($loanRequest->duration)->format('Y-m-d H:i:s'),
            'interest_percentage' => $loanRequest->interest_percentage,
            'emi' => $request->emi,
            'collection_plan' => $request->collection_plan,
            'collection_methods' => json_encode([
                ["code" => $request->collection_plan, "status" => 0, "type" => 'primary'],
                ["code" => $request->collection_plan_secondary, "status" => 0, "type" => 'secondary'],
            ]),
            'is_top_up' => $loanRequest->is_top_up,
            'top_up_loan_reference' => $loanRequest->loan_referenced,
            'duration' => $loanRequest->duration,
            'disburse_status' => 1,
            'status' => "0",
            'collector_type' => $loanRequest->placer_type,
            'collector_id' => $loanRequest->placer_id,
            'upfront_interest' => $loanRequest->upfront_interest
        ];

        return $data;
    }

}
