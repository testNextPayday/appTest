<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\Employer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Penalty\PenaltyService;

class PenaltySettingsController extends Controller
{

    protected $penaltyService;

    //
    public function __construct(PenaltyService $penaltyService)
    {
        $this->penaltyService  = $penaltyService;
    }

    
    /**
     * Create a new penalty service
     *
     * @param  mixed $request
     * @return void
     */
    public function create(Request $request)
    {  
        // dd('yes');
        try {
            
            if ($request->entity_type == 'Employer') {

                $model = Employer::find($request->entity_id);
    
            } else if ($request->entity_type == 'Loan') {
    
                $model = Loan::find($request->entity_id);
            } else {

                throw new \Exception('Cannot create settings for this object');
            }
           
            $data = $request->only(['type', 'value', 'grace_period', 'status', 'excess_penalty_status']);
            
            if (isset($request->setting_id)) {
                $data['id'] = $request->setting_id;
                $this->penaltyService->update($model, $data);
            }else  {

                $this->penaltyService->setup($model, $data);
            }
           
    
            return response()->json('Settings have been updated');

        }catch (\Exception $e) {
            return response()->json($e->getMessage(), 422);
        }
        
        
    }

    
    
}
