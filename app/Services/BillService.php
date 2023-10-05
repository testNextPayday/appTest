<?php
namespace App\Services;

use App\Models\Bill;
use App\Models\Admin;
use App\Traits\Managers\BillsManager;
use App\Unicredit\Payments\NextPayClient;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Admin\BillPaymentFailedNotification;


class BillService 
{
    /** Still retaining the previous bills manager to promote compatibility
     * where it was previously used
     */
    use BillsManager;
    
    /**
     * Payment Client for payment
     *
     * @var mixed
     */
    protected $paymentClient;

    
    /**
     * Initializes the payment client for payment
     *
     * @param  mixed $paymentClient
     * @return void
     */
    public function __construct(NextPayClient $paymentClient)
    {
        $this->paymentClient = $paymentClient;
    }

    
    /**
     * Pays a Bill
     *
     * @param  mixed $bill
     * @return void
     */
    protected function payBill(Bill $bill) 
    {
        return $this->paymentClient->payBill($bill);
    }

    
    /**
     * Monthly Bills Payer
     *
     * @return void
     */
    public function payMonthlyBills()
    {
        $bills = Bill::monthly()->get();

        foreach ($bills as $bill) {

            try {

               
                $response = $this->payBill($bill);

            }catch (\Exception $e) {

                //notify admin
               $admins = Admin::all();
               Notification::send($admins, new BillPaymentFailedNotification($bill, $e));
              
            }
           
        }
    }

    
    /**
     * Weekly Bills Payer
     *
     * @return void
     */
    public function payWeeklyBills()
    {
        $bills  = Bill::weekly()->get();

        foreach ($bills as $bill ) {
            try {

                $response = $this->payBill($bill);

            }catch (\Exception $e) {
               //notify admin
               $admins = Admin::all();
               Notification::send($admins, new BillPaymentFailedNotification($bill, $e));
            }
           
        }
    }


}