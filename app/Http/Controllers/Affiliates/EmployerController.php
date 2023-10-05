<?php

namespace App\Http\Controllers\Affiliates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Employers\StoreRequest;
use App\Models\Employer;

class EmployerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:affiliate');
    }
    
    public function store(StoreRequest $request)
    {
        
        $employerData = $request->all();
        
        if ($employer = Employer::create($employerData)) {
            return response()->json(['employer' => $employer], 200);
        }
        
        return response()->json(['message' => 'Employer could not be created'], 500);
    }
}
