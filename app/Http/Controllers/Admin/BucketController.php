<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Bucket;

class BucketController extends Controller
{
    public function index()
    {
        $buckets = Bucket::orderBy('name')->get();
        return view('admin.buckets.index', compact('buckets'));
    }
    
    public function show(Bucket $bucket)
    {
        return view('admin.buckets.show', compact('bucket'));
    }
    
    public function store(Request $request)
    {
        $rules = $this->getRules();
        
        $this->validate($request, $rules);
        
        $data = $request->only(array_keys($rules));
        
        if ($bucket = Bucket::create($data)) {
            return redirect()->back()->with('success', "$bucket->name created successfully");
        }
        
        return redirect()->back()->with('failure', "Bucket could not be created");
    }
    
    public function update(Request $request, Bucket $bucket)
    {
        if (!$bucket->exists)
            return redirect()->back()->with("failure", "Bucket not found");
            
        $data = $request->all();
        
        if ($bucket->update($data)) {
            
            return redirect()->back()->with('success', "Bucket updated successfully");
            
        }
        
        return redirect()->back()->with('failure', "Bucket could not be updated");
        
    }
    
    
    public function delete(Bucket $bucket)
    {
        try {
            
            $bucket->employers()->update(['bucket_id' => null]);
            
            $bucket->delete();
            
            return redirect()
                    ->back()
                    ->with('success', "Bucket deleted successfully");
                    
        } catch (Exception $e) {
            return redirect()
                    ->back()
                    ->with('failure', "Bucket could not be deleted. Error: " . 
                        $e->getMessage());
        }
    }
    
    private function getRules()
    {
        return [
            'name' => 'required',
            'sweep_start_day' => 'required|numeric',
            'sweep_end_day' => 'required|numeric',
            'sweep_frequency' => 'required|numeric',
            'peak_start_day' => 'required|numeric',
            'peak_end_day' => 'required|numeric',
            'peak_frequency' => 'required|numeric',
        ];
    }
}
