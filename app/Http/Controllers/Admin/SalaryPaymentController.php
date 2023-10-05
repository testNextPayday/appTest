<?php

namespace App\Http\Controllers\Admin;

use App\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Unicredit\Payments\NextPayClient;

class SalaryPaymentController extends Controller
{
    //
   

    public function __construct(NextPayClient $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        return view('admin.payments.salaries');
    }

    public function getStaffs()
    {

        $staffs = Staff::payable()->with('banks')->get();

        return response()->json($staffs);
    }


    public function updateSalary(Request $request, Staff $staff)
    {

        try{
            
            $staff->update(['salary'=>$request->salary]);

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json('Staff Salary Updated');
    }


    public function paySingleStaff(Request $request,Staff $staff)
    {

        try{

           $response =  $this->client->payStaffSalary($staff);

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response);
        
    }


    public function payStaffsInBulk(Request $request)
    {
        try{
           
            $response = $this->client->payStaffsBulk($request);

        }catch(\Exception $e){

            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($response['message']);
        
    }

}
