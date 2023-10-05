<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Employment;
use Illuminate\Http\Request;
use App\Events\SearchBorrowerLoan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\PayrollSearchResource;
use App\Http\Resources\BorrowerLoanTreeSelectResource;

class SearchController extends Controller
{
    //

    public function search(Request $request)
    {
        switch($request->query('type')){

            case 'payroll_id':
                $query = $request->query('query');
                $employments = Employment::where('payroll_id','like','%'.$query.'%')->with(['user','user.receivedLoans'])->limit(10)->get();
                $results = PayrollSearchResource::collection($employments);
            break;
            case 'borrowers':
                $query = $request->query('query');
                $users = User::where('name', 'like', '%' . $query . '%')->with('receivedLoans')->limit(10)
                    ->get();
                $results = BorrowerLoanTreeSelectResource::collection($users);
            break;
        }
       

        //broadcast search results with Pusher channels
        //event(new SearchBorrowerLoan($users));
        return response()->json($results,200);
    }

    public function getAllBorrower(Request $request)
    {
         
        $users = Cache::remember('users',20,function(){
            return BorrowerLoanTreeSelectResource::collection(User::all());
        });
        
        return response()->json($users);
    }
}
