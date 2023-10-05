<?php

namespace App\Http\Controllers\Admin;

use App\Models\Target;
use Illuminate\Http\Request;
use App\Services\TargetService;
use App\Http\Controllers\Controller;

class TargetController extends Controller
{
    //

    protected $service;

    public function __construct(TargetService $targetService)
    {
        $this->service = $targetService;
    }
    
    /**
     * Gets the index page
     *
     * @return void
     */
    public function index()
    {
        return view('admin.target.index');
    }
    
    /**
     * Updates a target
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Target $target
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Target $target)
    {
        try {

            $data = $request->except('_token');
            $this->service->update($target, $data);

            return response()->json('Update was successful');

        }catch (\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }
    }

    
    /**
     * Destroy a target
     *
     * @param  mixed $target
     * @return void
     */
    public function destroy(Target $target)
    {
        try {

            $this->service->delete($target);

            return response()->json('Delete was successful');

        }catch (\Exception $e) {

            return $this->sendJsonErrorResponse($e);
        }
    }

    
    /**
     * Returns all targets on the system
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTargets()
    {
        return response()->json($this->service->all());
    }
    
    /**
     * creates a target
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTarget(Request $request)
    {

        try {
            $data = $request->all();
            
            $this->service->create($data);

        }catch(\Exception $e) {
           
            return $this->sendJsonErrorResponse($e);
        }

        return response()->json('Target Created Successfully');
    }

    
    /**
     * show data about how a target is being used
     *
     * @param  mixed $request
     * @param  mixed $target
     * @return void
     */
    public function getTargetMetrics(Request $request, Target $target)
    {
        try {

            $data = $this->service->resolveMetrics($target);
            

        }catch (\Exception $e) {
           
            return $this->sendJsonErrorResponse($e);
        }

        return response()->json($data);
    }
}
