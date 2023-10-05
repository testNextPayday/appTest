<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    //

    public function __construct() 
    {
        $this->middleware('auth');
    }

    public function index(Request $request) 
    {
        $notifications = $request->user()->notifications;

        return view('users.notification.index', ['notifications'=>$notifications]);
    }


    public function markAsRead(Request $request)
    {
        try {

            $notification = $request->user()->unreadNotifications->where('id', $request->id);

            if($notification) {
                $notification->markAsRead();
            }
        }catch (\Exception $e) {

            return response()->json(['status'=>false], 422);

        }

        return response()->json(['status'=>true], 200);
        
    }
}
