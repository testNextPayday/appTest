<?php
namespace App\Repositories;

use App\Paystack\PaystackService;
use App\Unicredit\Contracts\CustomerRepository;


/**
 * PaystackCustomerRepository - Helps us retrieve our customer
 */
class PaystackCustomerRepository extends PaystackService implements CustomerRepository 

{

        
    /**
     * Get all customers 
     *
     * @return \Illuminate\Support\Facades\Collection
     */
    public function getAllCustomers()
    {
        $response = $this->setHttpResponse("/customer", "GET", $data= []);

        return collect($response->retrieveResponseData());
    }

    
    /**
     *  Gets customers with incomplete profile
     *
     * @return \Illuminate\Support\Facades\Collection
     */
    public function getIncompleteProfile()
    {
        return $this->getAllCustomers()->where('first_name', null)->values();
    }


    
    /**
     * Update a customers profile on paystack
     *
     * @param  string $code
     * @param  array $updateData
     * @return void
     */
    public function updateCustomer($code, $updateData)
    {
        $response = $this->setHttpResponse("/customer/$code", "PUT", $updateData);

        return $response->retrieveResponse();
    }
}
