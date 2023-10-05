<?php

namespace App\Http\Controllers\Affiliates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TargetAffiliateResource;

class TargetController extends Controller
{
    //
    
    /**
     * Active targets for an affiliate
     *
     * @return void
     */
    public function activeTargets()
    {
        $user = auth('affiliate')->user();
        $targets = TargetAffiliateResource::collection($user->targets->where('status', 1));
        return response()->json($targets);
        
    }
}
