<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BillApprovalRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\BillResource;
use App\Traits\Managers\BillsManager;
use Illuminate\Support\Facades\Cache;
use App\Unicredit\Payments\NextPayClient;
use App\Services\CacheManager\CacheConstants;

class BillsController extends Controller
{
    //

    use BillsManager; 
    
    public function __construct(NextPayClient $client)
    {
        $this->client  = $client;
    }

    public function index()
    {
        return view('admin.payments.bills');
    }


    public function statistics()
    {
        return view('admin.payments.bill-stats');
    }
    
    /**
     * Get cache data of bill statistics
     *
     * @return \Illuminate\Htpp\Response
     */
    public function statisticsData()
    {
        $response = Cache::get(CacheConstants::BILL_CAT_STATS);

        return response()->json($response);
    }

  


    public function payActiveBills(Request $request)
    {

       try{

            $banks = Bill::active()->with('banks')->get()->pluck('banks')->last();

            $request->request->add(['banks'=>$banks]);

            $response = $this->client->payBillsBulk($request);

       
       }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);

       }

       return response()->json($response);
    }


    public function paySingleBill(Request $request, Bill $bill) 
    {

        try {

            $response = $this->client->payBill($bill);

            $pendingRequests = $bill->requests->filter(function($req) { return $req->status == 'pending';});
            $pendingRequests->each(function($req) { $req->update(['status'=> 'paid']); });
            
        }catch (\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }

    }


    public function declineRequest(Request $request, BillApprovalRequest $billRequest) 
    {

        try {
           
            $billRequest->update(['status'=>'declined']);
            
        }catch (\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }

    }


   
}
