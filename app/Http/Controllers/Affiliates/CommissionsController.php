<?php

namespace App\Http\Controllers\Affiliates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AffiliateCommissionResource;

class CommissionsController extends Controller
{
    //

    public function index()
    {
        $user = auth('affiliate')->user();
        $commissions = $user->transactions()->with('entity')->get()->whereIn('code', ['019', '020'])->sortBy('created_at');
       
        // $commissions = AffiliateCommissionResource::collection($commissions);
        return view('affiliates.commissions.index', ['commissions'=>$commissions]);
    }
}
