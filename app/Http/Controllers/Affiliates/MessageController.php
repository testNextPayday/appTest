<?php

namespace App\Http\Controllers\Affiliates;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Message;
use App\Models\Admin;

use App\Events\MessageSent;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:affiliate');
    }
    
    public function index()
    {
        $affiliate = auth('affiliate')->user();
        
        $conversations = $affiliate->conversations();
        
        return view('affiliates.messages.index', compact('conversations'));
    }
    
    public function show($entityCode, $entityId)
    {
        $user = auth('affiliate')->user();
        
        $entityClass = @config('settings.entity_codes')[$entityCode];
        
        $entity = $entityClass::find($entityId);
        
        $user->markReceivedMessagesAsRead($entity);
        
        $messages = $user->conversationsWith($entity);
        
        return view('affiliates.messages.show', compact('entity', 'messages', 'user'));
    }
    
    public function sendMessageToAdmin(Request $request)
    {
        
        $rules = ['message' => 'required|string'];
        
        try {
            $this->validate($request, $rules);
            
            $affiliate = auth('affiliate')->user();
            
            $admin = Admin::first();
            
            $data = [
                'message' => $request->message,
                'receiver_id' => optional($admin)->id ?? 1,
                'receiver_type' => Admin::class
            ];
            
            $message = $affiliate->messages()->create($data);
        
            broadcast(new MessageSent($message))->toOthers();
            
            return response()->json(['message' => $message->fresh() ], 200);
            
        } catch (ValidationException $e) {
            
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
            
        } catch (\Exception $e) {
            
            return response()->json(['message' => $e->getMessage()], 500);
        }
        
    }
}
