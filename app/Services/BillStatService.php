<?php
namespace App\Services;

use App\Models\BillCategory;


class BillStatService
{    
    /**
     * Compute Bill Records
     *
     * @return void
     */
    public function computeBillRecords()
    {
        $response = [];

        // Get avaibale categories
        $categories = BillCategory::with('bills.gatewayRecords')->get();

        //For each bill get total amount and count of transactions
        foreach($categories as $category) {

            foreach($category->bills as $bill) {

                $count = $bill->gatewayRecords->count();

                $amount = $bill->gatewayRecords->sum('amount');
            }

            $response[$category->name] = ['total'=> $count, 'amount'=> $amount];
        }

        // return response
        return $response;
    }
}