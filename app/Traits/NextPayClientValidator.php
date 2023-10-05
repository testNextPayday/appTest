<?php
namespace App\Traits;


trait NextPayClientValidator
{

    public function validateRefund($refund)
    {
        $statusMap = [
            '0'=>'Pending',
            '1'=>'Approved',
            '2'=>'Rejected'
        ];

        if($refund->status != 1){

            throw new \DomainException(" Cannot pay for ".$statusMap[(string)$refund->status]." Refunds");
        }

        if(! isset($refund->amount)){

            throw new \DomainException(" Amount not set for Refund".$refund->loan->reference);
        }

        $bankDetails = $refund->user->bankDetails->last();

        if(! isset($bankDetails)){

            throw new \DomainException(" Loan owner has no account set ");
        }

        return true;
    }

    public function validateStaffSalary($staff)
    {

        if(!$staff->is_active){

            throw new \DomainException(" Cannot pay inactive staff: ".$staff->name);
        }

        if(! isset($staff->salary)){

            throw new \DomainException(" Salary not set for ".$staff->name);
        }

        $bankDetails = $staff->banks->last();

        if(! isset($bankDetails)){

            throw new \DomainException(" Beneficiary Staff has no account set ");
        }

        return true;

    }


    public function validateLoanPayment($loan)
    {

        $statusMap = [

            '0'=>'Processing',
            '1'=>'Active',
            '2'=>'Fulfilled',
            '3'=>'Defaulting',
            '4'=>'Cancelled'
        ];

        if($loan->status != '1'){

            throw new \DomainException(" Cannot pay for ".$statusMap[$loan->status]." Loans");
        }

        $bankDetails = $loan->user->bankDetails->last();

        if(! isset($bankDetails)){

            throw new \DomainException(" Loan owner has no account set ");
        }

        return true;
    }

    public function validateBillPayment($bill)
    {
        if(! isset($bill->amount)){

            throw new \DomainException(" Amount not set for ".$bill->name);
        }

        $bankDetails = $bill->banks->last();

        if(! isset($bankDetails)){

            throw new \DomainException(" Bill  has not bank account set ");
        }

        return true;
    }
}
?>