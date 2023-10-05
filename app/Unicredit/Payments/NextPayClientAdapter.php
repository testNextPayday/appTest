<?php
namespace App\Unicredit\Payments;

use App\Unicredit\Payments\NextPayClient;



class NextPayClientAdapter

{

    protected $paymentClient;

    public function __construct(NextPayClient $paymentClient)
    {
        $this->paymentClient  = $paymentClient;
    }

    
    /**
     * Handles the link to make payment
     *
     * @param  mixed $model
     * @return void
     */
    public function handle($model)
    {
        $method = $this->getAssocMethod($model);

        if ($method == '') {

            throw new \InvalidArgumentException('Cannot create new transactions for this kind of payment');
        }
        return $this->paymentClient->{$method}($model);
        
    }

    
    /**
     * Maps the class of the link to the underlying Payment client method
     *
     * @param  mixed $model
     * @return void
     */
    public function getAssocMethod($model)
    {
        switch (get_class($model)) {

            case 'App\Models\Loan': 
                $method = 'pushMoney';
            break;

            case 'App\Models\Refund':
                $method  = 'payRefund';
            break;

            case 'App\Models\Bill':
                $method = 'payBill';
            break;

            case 'App\Models\Staff':
                $method = 'payStaffSalary';
            break;

            default : 
                $method = '';
        }

        return $method;
    }
}