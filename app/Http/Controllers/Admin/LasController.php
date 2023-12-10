<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LasController extends Controller
{
    
    public function smsAlertEndpoint(Request $request) {

        $data = $request->all();

        $val =  Validator::make($data, [
            'phoneNumber' => 'required|string',
            'message'    => 'required|string',
            'institutionCode'   => 'required|string',
            'loanReference'   => 'required|string',

        ]);

        if ($val->fails()) {
            return response()->json($val->errors());
        }


        return response()->json([
            'status' => 200,
            'message' => 'Message recieved successfully',
        ]);
       
    }

    public function mandateCreation() {
        
    }

}
