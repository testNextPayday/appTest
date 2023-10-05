<?php

namespace App\Http\Controllers\Investors;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:investor');    
    }
    
    public function index()
    {
        $investor = auth()->guard('investor')->user();
        $collections = $investor->repayments()->latest()->paginate(20);
        
        return view('investors.collections.index', compact('collections'));
    }
}
