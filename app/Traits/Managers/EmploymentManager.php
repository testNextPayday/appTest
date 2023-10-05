<?php

namespace App\Traits\Managers;


use App\Models\Employment;
use Illuminate\Support\Arr;
use App\Services\ImageService;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Employments\UpdateRequest;

/**
 * Houses reusable code that can be used manage an employment
 * 
 */
trait EmploymentManager
{
    
    public function update(UpdateRequest $request, Employment $employment)
    {
        $user = $employment->user;
        
        $data = $request->all();
        
        $files = ['work_id_card', 'employment_letter', 'confirmation_letter'];
        
        foreach($files as $file) {
    
            $path = $this->updateFile($file, $request, $employment, $user->reference);
            
            if ($path)
                $data[$file] = $path;
        }
        
        if ($employment->update($data)) {
            return redirect()->back()->with('success', 'Update Successful');
        }
        
        return redirect()->back()->with('failure', 'Update failed');
    }
    
    private function updateFile($label, UpdateRequest $request, Employment $employment, $reference)
    {
        $file = null;

        $service = new ImageService(new Image);
        
        if ($request->hasFile($label) && $request->file($label)->isValid()) {
            
            if ($employment->getOriginal($label)) {
                Storage::delete(Arr::get($employment->getAttributes(), $label));    
            }

            $url = "public/documents/$reference/employment_data/";

            $file = $service->compressImage($request->$label, $url);
            
            // $file = $request->$label->store();
        }
        
        return $file;
    }

    public function updatePayRoll(Employment $employment)
    {
        
        $request = request();
        $request->validate([
            'payroll_id'=>'required|numeric'
        ]);
       $employment->update(['payroll_id'=>$request->payroll_id]);
       return redirect()->back()->with('success', ' Pay Roll ID updated');
    }
}