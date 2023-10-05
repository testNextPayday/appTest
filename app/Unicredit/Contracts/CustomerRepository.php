<?php 
namespace App\Unicredit\Contracts;

/** 
 * The customer repository represents a repo we fetch customers for sync
 */

 interface CustomerRepository 
 {
    
    /**
     * Gets all customers for the account
     *
     * @return \Illuminate\Support\Facades\Collection
     */
    public function getAllCustomers();

    
    
    /**
     * Update a particualr customer
     *
     * @param  string $code
     * @param  array $data
     * @return void
     */
    public function updateCustomer($code, $data);



 }