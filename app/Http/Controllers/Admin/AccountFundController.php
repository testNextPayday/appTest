<?php

namespace App\Http\Controllers\Admin;

use PDF, Mail;
use App\Models\User;

use App\Models\Staff;
use App\Models\Investor;
use App\Models\Affiliate;
use App\Mail\InvestmentMade;

use Illuminate\Http\Request;

use Illuminate\Http\Response;
use App\Models\PromissoryNote;
use App\Helpers\FinanceHandler;
use App\Traits\SettleAffiliates;
use App\Models\WalletTransaction;
use App\Helpers\TransactionLogger;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Notifications\Users\WalletFunded;
use App\Events\Investor\InvestorWalletFundEvent;
use App\Http\Resources\InvestorsWithWalletFunds;
use App\Http\Resources\InvestorsWithPromissoryNote;

class AccountFundController extends Controller
{

    use SettleAffiliates;
    
    public function fundWallet(Request $request, FinanceHandler $financeHandler)
    {
        $rules = [
            'reference' => 'required',
            'code' => 'required',
            'amount' => 'required'
        ];
        
        //find user
        $investor = Investor::whereReference($request->reference)->first();
        
        if (!$investor) return redirect()->back()->with('failure', 'Investor with reference number not found');
        
        $code = config('unicredit.flow')['wallet_fund'];
        $financeHandler->handleSingle(
            $investor, 'credit', $request->amount, null, 'W', $code
        );
        $investor->notify(new WalletFunded($request->amount, $request->code));

        event(new InvestorWalletFundEvent($investor, $request->amount));
        
        return redirect()->back()->with('success', 'Account funded successfully');
        
    }


    public function allNeededDataWalletFund(Request $request)
    {
        
        try {

            $activeInvestors  = Investor::active()->get();
            $investors = InvestorsWithWalletFunds::collection($activeInvestors);
            $affiliates = Affiliate::active()->get();
            $staffs = Staff::active()->get();

            $data = [
                'investors'=> $investors,
                'staffs'=> $staffs,
                'affiliates'=> $affiliates
            ];

            return response()->json($data);

        }catch(\Exception $e) {

            return response()->json(['failure'=>$e->getMessage()], 422);
        }
        
    }


    public function allNeededDataPromissoryNote(Request $request)
    {
        
        try {

            $activeInvestors  = Investor::promissoryNote()->active()->get();
            $investors = InvestorsWithPromissoryNote::collection($activeInvestors);
            $affiliates = Affiliate::active()->get();
            $staffs = Staff::active()->get();

            $data = [
                'investors'=> $investors,
                'staffs'=> $staffs,
                'affiliates'=> $affiliates
            ];

            return response()->json($data);

        }catch(\Exception $e) {

            return response()->json(['failure'=>$e->getMessage()], 422);
        }
        
    }


    public function payFundCommission(Request $request)
    {
        try {
            DB::beginTransaction();

            $transaction = WalletTransaction::find($request->fund_id);


            if ($transaction) {

                $investor  = $transaction->owner;

                if (! $investor->getReferrer()->name) {

                    throw new \Exception('Ensure the investor was referred by this person');
                }

                $referrer = $investor->referrer;

                $amount = $transaction->amount;

                $model = $this->determineReceivingModel($request->receiverType);

                $person = $model->find($request->assignedPersonId);

                if ($person == $referrer) {

                    $financeHandler = new FinanceHandler(new TransactionLogger);

                    $this->settleAffiliateOnFunding($investor, $amount, $financeHandler);

                    DB::commit();

                    return redirect()->back()->with('success', 'Successfully paid commission');
                }

                throw new \Exception('Only referrer can be settled using this module');

            } else {

                throw new \InvalidArgumentException('Transaction not found');
            }

        }catch (\Exception $e) {

            DB::rollback();
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }


    public function payPNoteCommission(Request $request)
    {
        try {
            DB::beginTransaction();

            $promissoryNote = PromissoryNote::find($request->note_id);


            if ($promissoryNote) {

                $investor  = $promissoryNote->investor;

                if (! $investor->getReferrer()->name) {

                    throw new \Exception('Ensure the investor was referred by this person');
                }

                $referrer = $investor->referrer;

                $amount = $promissoryNote->principal;

                $tenure = $promissoryNote->tenure;

                $model = $this->determineReceivingModel($request->receiverType);

                $person = $model->find($request->assignedPersonId);

                if ($person == $referrer) {

                    $financeHandler = new FinanceHandler(new TransactionLogger);

                    $this->settleAffiliateOnPromissoryNote($referrer, $promissoryNote, $amount, $tenure, $financeHandler);

                    DB::commit();

                    return redirect()->back()->with('success', 'Successfully paid commission');
                }

                throw new \Exception('Only referrer can be settled using this module');

            } else {

                throw new \InvalidArgumentException('Transaction not found');
            }

        }catch (\Exception $e) {

            DB::rollback();
            return redirect()->back()->with('failure', $e->getMessage());
        }
    }


    protected function determineReceivingModel($receiver)
    {
       
        switch($receiver) {
            case 'affiliate':
                $model = new Affiliate;
            break;

            case 'staff':
                $model = new Staff;
            break;

            case 'investor':
                $model = new Investor;
            break;

            default:
                throw new \InvalidArgumentException('No model found');
        }

        return $model;
    }
}
