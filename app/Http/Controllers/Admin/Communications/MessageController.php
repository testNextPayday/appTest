<?php

namespace App\Http\Controllers\Admin\Communications;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Events\MessageSent;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');    
    }
    
    public function conversations()
    {
        $admin = auth('admin')->user();
        
        $conversations = $admin->conversations();
        
        return view(
            'admin.communications.conversations.index',
            compact('conversations')
        );
    }
    
    public function show($entityCode, $entityId)
    {
        $user = auth('admin')->user();
        
        $entityClass = @config('settings.entity_codes')[$entityCode];
        
        $entity = $entityClass::find($entityId);
        
        $user->markReceivedMessagesAsRead($entity);
        
        $messages = $user->conversationsWith($entity);
        
        return view(
            'admin.communications.conversations.show',
            compact('entity', 'messages', 'user', 'entityCode')
        );
    }
    
    
    public function send(Request $request, $entityCode, $entityId)
    {
        $rules = ['message' => 'required|string'];
        
        try {
        
            $this->validate($request, $rules);
            
            $admin = auth('admin')->user();
            
            $entityClass = @config('settings.entity_codes')[$entityCode];
            
            $entity = $entityClass::find($entityId);
            
            if (!$entity)
                return redirect()->back()->with('failure', 'Receipient Not Found');
            
            
            $data = [
                'message' => $request->message,
                'receiver_id' => $entity->id,
                'receiver_type' => get_class($entity)
            ];
            
            $message = $admin->messages()->create($data);
            
            broadcast(new MessageSent($message))->toOthers();
            
            return response()->json(['message' => $message->fresh() ], 200);
            
        } catch(ValidationException $e) {
            
            return response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422);
            
        } catch (\Exception $e) {
            
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
