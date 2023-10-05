<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Unicredit\Contracts\CustomerRepository;

class PaystackCustomerSyncController extends Controller
{
    //

    
    /**
     * Gives us the index page for sync
     */
    public function index()
    {
        return view('admin.payments.sync');
    }


    
    /**
     * Gets all profile customers
     * 
     * @param  \App\Unicredit\Contracts\CustomerRepository $repository
     * @return \Illuminate\Http\JsonResponse;
     */
    public function getAllCustomers(CustomerRepository $repository)
    {
        try {

            $customers = $repository->getAllCustomers();

            return response()->json(['status'=>true, 'data'=>$customers]);

        }catch (\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }
    }


    
    /**
     * Gets incomplete profile customers
     * 
     * @param  \App\Unicredit\Contracts\CustomerRepository $repository
     * @return \Illuminate\Http\JsonResponse;
     */
    public function incompleteProfileCustomers(CustomerRepository $repository)
    {
        try {

            $customers = $repository->getIncompleteProfile();
           
            return response()->json(['status'=>true, 'data'=>$customers]);

        }catch (\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }
    }

    
    /**
     * This method syncs customer to paystack
     *
     * @param  \App\Unicredit\Contracts\CustomerRepository $repository
     * @return \Illuminate\Http\JsonResponse;
     */
    public function sync(CustomerRepository $repository)
    {

        try {

            $updates = 0;

            $customers = $repository->getIncompleteProfile();
            

            foreach ($customers as $customer) {

                try {

                    $details = User::where('email', $customer['email'])->first();

                    if ($details) {

                        // split the name to firstname and lastname
                        @list($firstname,$lastname) =  explode(",", $details->name);

                        $data = [
                            'first_name'=>$firstname, 
                            'last_name'=>$lastname,
                            'phone'=> $details->phone
                        ];

                        $response = $repository->updateCustomer(
                            $customer['customer_code'], $data
                        );

                        

                        if ($response['status']) {

                            $updates++;
                        }
                    }
                }catch (\Exception $e) {
                    return $this->sendJsonErrorResponse($e);
                    continue;
                }
            }


        }catch (\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json(['status'=>true, 'message'=> $updates.' profiles updated']);
    }
}
