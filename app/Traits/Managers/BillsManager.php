<?php
namespace App\Traits\Managers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BillApprovalRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\BillRequestResource;


trait BillsManager
{

    public function getBanks()
    {
        $data = config('remita.banks');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/bank?currency=NGN",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
                "Cache-Control: no-cache",
            ),
        )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $res = json_decode($response);
        



        // $data = json_encode($banks);

        // dd($response); 

        // if ($err) {
        //     echo "cURL Error #:" . $err;
        // } else {
        //     echo $response;
        // }


        return response()->json($res->data );
    }

    public function getRecords()
    {
        $bills = BillResource::collection(Bill::all());
        return response()->json($bills, 200);
    }

    public function getBillTransactions(Request $request, Bill $bill)
    {

        return response()->json($bill->gatewayRecords, 200);
    }

    public function requestPayment(Request $request, Bill $bill)
    {
        try {

            $request = BillApprovalRequest::create(['bill_id' => $bill->id]);

        } catch (\Exception $e) {
            return $this->sendJsonErrorResponse($e);
        }
    }


    /**
     * pendingRequests
     *
     * @param  mixed $request
     * @return void
     */
    public function pendingRequests(Request $request)
    {
        $pendingRequests = BillApprovalRequest::pending()->get();
        $data = BillRequestResource::collection($pendingRequests);

        return response()->json($data, 200);
    }

    /**
     * declinedRequests
     *
     * @param  mixed $request
     * @return void
     */
    public function declinedRequests(Request $request)
    {
        $declinedRequests = BillApprovalRequest::declined()->get();
        $data = BillRequestResource::collection($declinedRequests);
        return response()->json($data, 200);
    }


    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            $bill = Bill::create($request->only(['name', 'amount', 'frequency', ' occurs', 'bill_category_id']));

            $billBankDetails = $request->only(['bank_code', 'account_number']);

            $billBankDetails['bank_name'] = config('remita.banks')[$request->bank_code];

            $billBankDetails['owner_id'] = $bill->id;

            $billBankDetails['owner_type'] = get_class($bill);

            $bill->addBeneficiaryAccount($billBankDetails);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            return $this->sendJsonErrorResponse($e);

        }

        return response()->json([], 200);
    }




    public function update(Request $request, Bill $bill)
    {
        try {

            DB::beginTransaction();

            $bill->update(
                $request->only(['name', 'amount', 'frequency', 'occurs', 'status', 'bill_category_id'])
            );



            $billBankDetails = $request->only(['bank_code', 'account_number']);

            $billBankDetails['bank_name'] = config('remita.banks')[$request->bank_code];

            //$billBankDetails['bank_code'] = null;

            $bill->banks->last()->update($billBankDetails);

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json([], 200);
    }


    public function delete(Bill $bill)
    {
        try {

            DB::beginTransaction();

            $bill->banks->each(function ($bank) {

                return $bank->delete();
            });

            $bill->delete();

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json([], 200);
    }
}