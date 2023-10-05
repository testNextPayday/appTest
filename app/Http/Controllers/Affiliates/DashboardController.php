<?php

namespace App\Http\Controllers\Affiliates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Settings;
use DB;
use App\Helpers\FinanceHandler;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:affiliate');    
    }
    
    public function index()
    {
        return view('affiliates.dashboard');
    }
    
    public function waitingArea($condition)
    {
        $user = auth('affiliate')->user();
        
        if (!$user) {
            return redirect()->route('affiliates.login');
        }
        
        
        if ($user->verified_at && $user->status) {
            return redirect()->route('affiliates.dashboard');    
        }
        
        return view('affiliates.waiting-area', compact('condition', 'user'));
    }
    
    public function applyForVerification(FinanceHandler $financeHandler) 
    {
        $response['message'] = '';
        
        $user = auth('affiliate')->user();
        
        
        $fee = Settings::affiliateVerificationFee();
        
        if($user->wallet < $fee) {
            
            $response['message'] = 'You have insufficient funds. Please fund your wallet!';
            return response()->json($response, 400);
            
        }
        
        try {
            
            DB::beginTransaction();
            
            $code = config('unicredit.flow')['affiliate_verification'];
        
            $financeHandler->handleSingle(
                $user, 'debit', $fee, null, 'W', $code
            );
            
            $user->update(['verification_applied' => true]);
            
            DB::commit();
            
            $response['message'] = 'Application successful';
            
            return response()->json($response, 200);
            
        } catch(\Exception $e) {
            
            $response['message'] = $e->getMessage();
            return response()->json($response, 500);
            
        }
    }
}
