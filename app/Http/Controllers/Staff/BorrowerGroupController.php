<?php

namespace App\Http\Controllers\Staff;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Loan;
use App\Models\BorrowerGroup;

class BorrowerGroupController extends Controller
{
    public function getActiveLoans(){        
        $users = User::get(['name','id', 'reference']);
        return view('staff.groups.add', compact('users'));
    }
    public function search(Request $request)
    {
        $users = User::where('name', 'LIKE','%'.$request->keyword.'%')->get();        
        return response()->json($users);
         
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users);         
    }

    public function viewGroups(Request $request){
        
        $searchTerm = $request->get('q') ?? '';
        $groups = BorrowerGroup::query();        
        if ($searchTerm) {
            $groups = $groups->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')                        
                        ->orWhere('user_references', 'like', '%' . $searchTerm . '%');
            });
        }        
        $groups = $groups->latest()->paginate(20);
        return view('staff.groups.view', compact('groups', 'searchTerm'));
    }

    public function viewSingleGroup($reference)
    {          
        
        $group = BorrowerGroup::where('reference',$reference)->first();
      
        if(!$group) return redirect()->back()->with('failure', 'Loan not found');
        
        $userReferences = json_decode($group->user_references);  
        $users = User::whereIn('reference', $userReferences)->get(['reference', 'name' ]);
           
        return view('staff.groups.single-view', compact('users'));
    }
    public function storeGroupedBorrowers(Request $request){
        try{
            DB::beginTransaction();
            $userReferences = $request->groupedBorrowers;            
            $groupName = $request->group_name;            
            $data = [                
                'user_references' => json_encode($userReferences),
                'name'=> $groupName
            ];            
            BorrowerGroup::create($data);

            DB::commit();
            return response()->json([ 'status'=> 1,'message'=>' Group Created Successfully'], 200);
        }catch(\Exception $e) {
            DB::rollback();
            return response()->json([ 'status'=> 0,'message'=>$e->getmessage()], 500);
        }
    }

}
