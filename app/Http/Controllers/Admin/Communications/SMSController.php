<?php

namespace App\Http\Controllers\Admin\Communications;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Investor;
use App\Repositories\SmsRepository;
use App\Jobs\SendTermiiSms;

class SMSController extends Controller
{
    public function getSMS()
    {   
        $users = User::all();
        $investors = Investor::all();
        return view('admin.communications.sms.index')->with([
            'users' => $users,
            'investors' => $investors
        ]);
    } 
    
    public function sendSMS(Request $request)
    {
        try {

            $numbers = [];
        
            // to avoid breaking stuff i would just put this in the session and retrieve later
            session()->put('sms_sender', $request->sender);
            
            if ($request->borrowers == 'on') {
                $borrowers = User::pluck('phone')->toArray();
                $numbers = array_merge($numbers, $borrowers);
            }        
            
            if ($request->investors == 'on') {
                $investors = Investor::pluck('phone')->toArray();
                $numbers = array_merge($numbers, $investors);
            }
            
            if ($request->investorsearch) {
                $numbers = $request->investorsearch;
            }

            if ($request->borrowersearch) {
                $numbers = $request->borrowersearch;
            }

            $readyNumbers = array_filter(array_unique($numbers));
            
            $readyNumbers = array_map(function ($number) {

                  return "234" . substr(str_replace(" ", "", trim($number)), -10); 
                }, $readyNumbers
            );
            
            dispatch(
                new SendTermiiSms(
                    $readyNumbers, 
                    $request->message, 
                    $request->sender
                )
            )
            ->delay(60);
            
        } catch(\Exception $e) {

            return $this->sendExceptionResponse($e);
        }
        
        return redirect()->back()->with(
            'success', 
            count($readyNumbers) . " SMS Message(s) scheduled for sending in 1 minute"
        );
    }

}
