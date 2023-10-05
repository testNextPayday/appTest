<?php

namespace App\Http\Controllers;

use Auth;
use Paystack;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Employer;
use App\Models\Investor;
use Illuminate\Http\Request;

use App\Models\InvestWithMono;
use App\Traits\SendsResponses;
use App\Helpers\FinanceHandler;
use App\Models\GatewayTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PromissoryNoteSetting;
use Unicodeveloper\Paystack\TransRef;
use App\Services\LoanRepaymentService;
use App\Services\ResolveAccountService;
use App\Events\Investor\InvestorWalletFundEvent;
use App\Services\Investor\PromissoryNoteService;
use App\Services\InstallmentPaymentVerifyService;

class PaystackController extends Controller
{

    use SendsResponses;
    protected $promissoryService;

    public function __construct(PromissoryNoteService $promissoryService)
    {        
        $this->promissoryService = $promissoryService;
    }

    public function redirectToGateway(Request $request) {
        session([
            'user_id' => $request->user_id,
            ]);
        
       // $request->request->add(['amount' => (double)$request->amount  + ((double)$request->amount * 1.5) + (100)]);
      return Paystack::getAuthorizationUrl()->redirectNow();
    }

    public function getTransRef()
    {
        $reference = TransRef::getHashedToken();

        return response()->json($reference);
    }

    /**
    * Obtain Paystack payment information
    * @return void
    */
    public function handleGatewayCallback() 
    {
      
      $paymentDetails = Paystack::getPaymentData();
      $amount = ($paymentDetails['data']['amount'])/100;
      
      $guard = Auth::guard('web')->check() ? 'web' : 'investor';
      $type = Auth::guard('web')->check() ? 'User' : 'Investor';
      
      $user = Auth::guard($guard)->user();
      $user->wallet += $amount;
      
      $transaction = new GatewayTransaction;
      $transaction->amount = $amount;
      $transaction->owner_id = $user->id;
      $transaction->owner_type = "App\Models\{$type}";
      $transaction->reference = $paymentDetails['data']['reference'];
      $transaction->transaction_id = $paymentDetails['data']['id'];
      $transaction->description = 'Wallet Funding';
      
      
      if ($paymentDetails['status'] == true) {
          
          $transaction->pay_status = 1;
          $user->update();
          $transaction->save();
          return redirect()->route('users.dashboard')->with('success', 'Successfully Funded Wallet.');
          
      } else { 
          //payent did not go through
          $transaction->pay_status = 0;
          $transaction->save();
          return redirect()->route('users.dashboard')->with('failure', 'Unable to fund wallet. Please try again later!');
      }
      
    }
    
    /**
     *Verify Paystack Payment 
     * 
     */
    public function verifyEmployerVerificationPayment(Request $request) 
    {
        $result = $this->verifyPayment($request);
        $transactionData = [
            'amount' => $request->amount,
            'user_id' => Auth::id(),
            'reference' => $request->reference,
            'description' => 'Employee verification fee'
        ];
        if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {
            $transactionData['pay_status'] = 1;
            $transactionData['transaction_id'] = $result['data']['id'];
            GatewayTransaction::create($transactionData);
            
            $employer = Employer::find($request->employer_id);
            if($employer) {
                $employer->update(['is_verified' => 1,'user_request' => true,'user_id' => Auth::id()]);    
            } else {
                return response()->json([
                    'status' => 0, 
                    'message' => 'Payment was successful but employer was not found. Please contact admin to rectify this'
                ], 200);
            }
            return response()->json(['status' => 1, 'message' => 'Payment Successful', 'employer' => $employer], 200);
        }
        $transactionData['pay_status'] = 0;
        $transactionData['transaction_id'] = 'Failed';
        GatewayTransaction::create($transactionData);
        return response()->json(['status' => 0, 'message' => 'Payment Failed'], 200);
    }


    public function verifyUserPlansPayment(Request $request, InstallmentPaymentVerifyService $verifyService)
    {

        try{
            DB::beginTransaction();
                $response = $verifyService->verifyInstallment($request->all());
            DB::commit();
            $code = $response['status'] == 1 ? 200 : 422;
            return response()->json($response, $code);
            
        }catch (\Exception $e) {

            return response()->json(['status'=> 0 , 'message'=> 'An error Occured Try Again']);
        }
        
    }
    
    /**
     * Retrieves gateway data 
     *
     * @param  mixed $data
     * @return void
     */
    protected function getGatewayData(array $data) 
    {
        $transactionData = array();
        $transactionData['reference'] = $result['data']['reference'];
        $transactionData['amount'] = $result['data']['amount'];
        $transactionData['status_message'] = $result['data']['status'];
        $transactionData['transaction_id'] = $result['data']['reference'];
        $transactionData['description'] = 'Bulk Repayment Payment';
        $transactionData['owner_id'] = request()->user()->id;
        $transactionData['owner_type'] = get_class(request()->user());
        return $transactionData;
    }
    
    public function investorVerifyFundTransaction(Request $request){
        $guard = "web";
        
        if (auth('investor')->check()) {
            $guard = "investor";
        } else if (auth('affiliate')->check()) {
            $guard = "affiliate";
        }
        //Log::info('I got to this point');
        Log::info(json_encode($request->all()));
        $person = auth($guard)->user();
        $amount = $request->amount;
                        
        $startDate = $request->startDate;
        $tenure = $request->tenure;
        $reference = $request->reference;
        $interestPaybackCycle = $request->interestPaybackCycle;
        $result = $this->verifyPayment($request);

        if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {             
                if($interestPaybackCycle == 'upfront'){
                    $settings = PromissoryNoteSetting::where('name', 'like','%Upfront%')->first();
                    $rate = $settings->interest_rate;
                    $tax = $settings->tax_rate;
                }
                if($interestPaybackCycle == 'backend'){
                    $settings = PromissoryNoteSetting::where('name', 'like','%Backend%')->first();
                    $rate = $settings->interest_rate;
                    $tax = $settings->tax_rate;
                }
                if($interestPaybackCycle == 'monthly'){
                    $settings = PromissoryNoteSetting::where('name', 'like','%Monthly%')->first();
                    $rate = $settings->interest_rate;
                    $tax = $settings->tax_rate;
                }
                $invest = InvestWithMono::create([
                    'amount' => $amount,
                    'start_date' => $startDate,
                    'tenure' => $tenure,
                    'interest_payback_cycle' => $interestPaybackCycle,
                    'reference' => $reference,
                    'investor_id' => $person->id,
                    'payment_type'=>'Paystack',
                ]);

                $data = [
                    'start_date' => $startDate,
                    'amount' => $amount,
                    'tenure' => $tenure,
                    'investor_id'=> $person->id,
                    'interest_payment_cycle'=>$interestPaybackCycle,
                    'rate'=> $rate,
                    'tax'=> $tax,
                    'payment_type'=>'Paystack',
                    'invest_id'=>$invest->id,
                ];
                if (!$person = Investor::find($person->id)) {
                    throw new \InvalidArgumentException('Could not find Investor');
                }
    
                if ($response = $this->promissoryService->createPromissoryNote2($data, $person)) {
            
                    return response()->json(['status' => 1, 'message' => 'Payment Successful'], 200);
                }
        }
        
        // $transactionData['pay_status'] = 0;
        // $transactionData['transaction_id'] = 'Failed';
        // GatewayTransaction::create($transactionData);
        return response()->json(['status' => 0, 'message' => 'Payment Failed'], 200);


    }

   

    
    
    public function verifyWalletFundTransaction(Request $request, FinanceHandler $financeHandler)
    {
        $guard = "web";
        
        if (auth('investor')->check()) {
            $guard = "investor";
        } else if (auth('affiliate')->check()) {
            $guard = "affiliate";
        }
        
        $person = auth($guard)->user();
        
        $result = $this->verifyPayment($request);
        
        // $transactionData = [
        //     'amount' => $request->amount,
        //     'user_id' => $user->id,
        //     'reference' => $request->reference,
        //     'description' => 'Wallet funding'
        // ];
        
        if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {
            // $transactionData['pay_status'] = 1;
            // $transactionData['transaction_id'] = $result['data']['id'];
            // GatewayTransaction::create($transactionData);
            
            $code = config('unicredit.flow')['wallet_fund'];
            $financeHandler->handleSingle(
                $person, 'credit', $request->amount, null, 'W', $code
            );

            if (($guard == 'investor')) {

               

                event(new InvestorWalletFundEvent($person, $request->amount));
                
                
            }
            
            return response()->json(['status' => 1, 'message' => 'Payment Successful'], 200);
        }
        
        // $transactionData['pay_status'] = 0;
        // $transactionData['transaction_id'] = 'Failed';
        // GatewayTransaction::create($transactionData);
        return response()->json(['status' => 0, 'message' => 'Payment Failed'], 200);
    }
    
    private function verifyPayment(Request $request) 
    {
        $reference = $request->reference;    
        
        $result = array();
        //The parameter after verify/ is the transaction reference to be verified
        $url = 'https://api.paystack.co/transaction/verify/' . $reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
          $ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . config('paystack.secretKey')]
        );
        $request = curl_exec($ch);
        curl_close($ch);
        $result = [];
        if ($request) {
          $result = json_decode($request, true);
        }
        return $result;
    }

    
    /**
     * From here henceforth i will be injecting services 
     * to do whatever i want to do
     *
     * @param  mixed $resolver
     * @return void
     */
    public function resolveAccount(Request $request, ResolveAccountService $resolver)
    {

        try {

            $data = $request->only(['account_number', 'bank_code']);

            return $resolver->resolveAccount($data);

        } catch(\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }
        
    }
}