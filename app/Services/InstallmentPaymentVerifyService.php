<?php
namespace App\Services;

use Illuminate\Http\Request;
use App\Models\GatewayTransaction;
use Illuminate\Support\Facades\Auth;
use App\Services\TransactionVerificationService;


class InstallmentPaymentVerifyService
{

    protected $verifyService;
    
    public function __construct(TransactionVerificationService $verifyService)
    {
        $this->verifyService = $verifyService;
    }

    
    /**
     * This is verify installment
     *
     * @param  mixed $request
     * @return void
     */
    public function verifyInstallment(array $request)
    {
        
        $result = $this->verifyService->verifyTransaction($request['reference']);

        $transactionData = $this->getGatewayData($result);

        if ($this->verifyService->verificationSuccessful()) {
        
            //log it in the logger
            $transactionData['pay_status'] = 1;
            GatewayTransaction::create($transactionData);
            
            return ['status' => 1, 'message' => 'Payment Successful'];
        }

        $transactionData['pay_status'] = 0;
        GatewayTransaction::create($transactionData);
        
        return ['status' => 0, 'message' => 'Payment Verification Failed'];
    }


    /**
     * Retrieves gateway data 
     *
     * @param  mixed $data
     * @return void
     */
    protected function getGatewayData(array $result) 
    {
        $transactionData = array();
        $transactionData['reference'] = $result['data']['reference'];
        $transactionData['amount'] = $result['data']['amount'];
        $transactionData['status_message'] = $result['data']['status'];
        $transactionData['transaction_id'] = $result['data']['reference'];
        $transactionData['description'] = 'Bulk Repayment Payment';
        $transactionData['owner_id'] = Auth::user()->id;
        $transactionData['owner_type'] = get_class(Auth::user());
        return $transactionData;
    }
}