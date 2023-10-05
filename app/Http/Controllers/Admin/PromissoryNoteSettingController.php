<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PromissoryNoteSetting;
use App\Services\Investor\PromissoryNoteService;

class PromissoryNoteSettingController extends Controller
{
    
    /**
     * Injects a promissory note service
     *
     * @param  mixed $service
     * @return void
     */
    public function __construct(PromissoryNoteService $service)

    {
        $this->promissoryService = $service;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $settings   = PromissoryNoteSetting::all();

        return view('admin.promissory-notes.settings', ['settings'=> $settings]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $data = $request->only(['name', 'interest_rate', 'tax_rate']);

            $this->promissoryService->createSettings($data);

        }catch(\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Settings has been created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PromissoryNoteSetting $setting)
    {
        try {

            $data = $request->only(['name', 'interest_rate', 'tax_rate']);

            $this->promissoryService->updateSettings($data, $setting);

        }catch(\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Settings has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PromissoryNoteSetting $setting)
    {
        try {

            $this->promissoryService->deleteSettings($setting);

        }catch(\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Settings has been deleted');
    }
}
