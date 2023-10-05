<?php
namespace App\Unicredit\Payments;

use App\Models\Bill;
use App\Models\Loan;
use App\Models\Staff;
use App\Models\Refund;
use App\Models\BankDetail;
use App\Helpers\FinanceHandler;
use App\Unicredit\Managers\MoneyGram;
use App\Traits\NextPayClientValidator;


class NextPayClient

{
    use NextPayClientValidator;
    
    public function __construct(FinanceHandler $financeHandler, MoneyGram $moneyGram)
    {

        $this->financeHandler = $financeHandler;

        $this->moneyGram = $moneyGram;

    }


    public function payBill(Bill $bill)
    {

        $amount = $bill->amount;

        $this->validateBillPayment($bill);

        $billData = [
            'amount'=> $bill->amount,
            'type'=> $bill->name.' Bill Payment',
            'link'=> $bill,
            'reason'=> 'Bill Payment '
        ];

        $bankDetails = $bill->banks->last();

        $gatewayResponse  = $this->moneyGram->makeTransfer($bankDetails, $billData);

        return $gatewayResponse;

    }

    public function payStaffSalary(Staff $staff)
    {

        $amount = $staff->salary;

        $this->validateStaffSalary($staff);

        $staffData = [
            'amount'=> $amount,
            'type'=> $staff->name.' Salary Payment',
            'link'=> $staff,
            'reason'=> 'Staff Salary Payment'
        ];

        $bankDetails = $staff->banks->last();

        $gatewayResponse  = $this->moneyGram->makeTransfer($bankDetails, $staffData);

        return $gatewayResponse;        
    }


    public function payRefund(Refund $refund)
    {

        $amount = $refund->amount;

        $this->validateRefund($refund);

        $refundData = [
            'amount'=> $amount,
            'type'=> $refund->loan->reference.' Refund Payment',
            'link'=> $refund,
            'reason'=> 'Loan Refund Payment'
        ];

        $bankDetails = $refund->user->bankDetails->last();
       
        $gatewayResponse  = $this->moneyGram->makeTransfer($bankDetails, $refundData);

        return $gatewayResponse;

    }


    /** This method is called by the FundDisbursedLoanListener */
    public function pushMoney(Loan $loan)
    {

        $amount = round($loan->disbursal_amount, 2);

        $this->validateLoanPayment($loan);

        $loanData = [
            'amount'=> $amount,
            'type'=> $loan->reference.' Loan Disbursement',
            'link'=> $loan,
            'reference' => generateHash(),
            'reason'=> 'Loan disbursement'
        ];
        $bankDetails = $loan->user->bankDetails->last();

        $gatewayResponse  = $this->moneyGram->makeTransfer($bankDetails, $loanData);

        
        return $gatewayResponse;
    }


    public function payBillsBulk($request)
    {
     
        $transferData['type'] = 'Bill Payment';

        $transferData['transfers'] = $this->getBulkBillData($request);
        
        $response = $this->moneyGram->makeBulkTransfer($transferData);

        return $response;   
    }


    public  function payStaffsBulk($request)
    {
        $transferData['type'] = 'Salary Payment';
        
        $transferData['transfers'] = $this->getBulkStaffData($request);

        $response = $this->moneyGram->makeBulkTransfer($transferData);

        return $response;
    }



    private function getBulkBillData($request)
    {
        $transferList = [];

        foreach($request->banks as $index=>$bank)
        {
            $transfer = array();
            $transfer['amount'] = $bank->owner->amount;
            $transfer['recipient'] = $bank->recipient_code;
            $transfer['reference'] = generateHash();

            $transferList[] = $transfer;
        }

        return $transferList;

    }


    private function getBulkStaffData($request)
    {

        $transferList = [];
      
        foreach($request->banks as $index=>$bank_id)
        {
            $bank = BankDetail::find($bank_id);
           
            $transfer = array();
            $transfer['amount'] = $bank->owner->salary;
            $transfer['recipient'] = $bank->recipient_code;
            $transfer['reference'] = generateHash();

            $transferList[] = $transfer;
        }

        return $transferList;
    }

}
?>