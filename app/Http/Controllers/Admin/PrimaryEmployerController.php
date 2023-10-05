<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PrimaryEmployer;

class PrimaryEmployerController extends Controller
{
    //
   
    public function index(){
        $primaryEmployers = PrimaryEmployer::all();
        return view('admin.employers.primary',compact('primaryEmployers'));
    }
    
    public function store(Request $request){
         try {
            
            $validationRules = $this->getRules();
            $this->validate($request, $validationRules);
            
            $data = $request->only(array_keys($validationRules));
          
            
            $employer = PrimaryEmployer::create($data);
            
            return redirect()->back()->with('message','Created Succcessfully');
            
        } catch(ValidationException $e){
            return redirect()->back()-withErrors();
        }
    }
    public function getRules(){
        return [
            'name' => 'required|string',
        ];
    }
    public function delete($id){
        $primaryEmployer = PrimaryEmployer::find($id);
        $primaryEmployer->delete();
        return redirect()->back();
    }
}
