<?php

namespace App\Traits;

use App\Models\Message;
use Illuminate\Support\Collection;
use DB;

trait Conversations {
    
    public function conversations()
    {
        $className = get_class($this);
        $id = $this->id;
        
        $messageData = $this->fetchGroupedConversations($className, $id);
                        
        return $this->attachParticipantAndLastMessage($messageData, $className, $id);
                    
    }
    
    
    
    public function fetchGroupedConversations($className, $id)
    {
        $caseClassName = $this->getCaseClassName($className);
        
        return Message::select(
                    DB::raw('count(*) as total_count'),
                    DB::raw(
                        "(CASE WHEN sender_type='$caseClassName' THEN receiver_type ELSE sender_type END) AS typeString"
                    ),
                    DB::raw(
                        "(CASE WHEN sender_type='". 
                        $caseClassName .
                        "' THEN receiver_id ELSE sender_id END) AS idString"
                    ),
                    DB::raw('max(created_at) as last_conversation_time')
                )
                
                ->where(function($query) use ($className, $id) {
                   return $query->where('sender_type', $className)->where('sender_id', $id); 
                })
                
                ->orWhere(function($query) use ($className, $id) {
                    return $query->where('receiver_type', $className)->where('receiver_id', $id);
                })
                
                ->orderBy('last_conversation_time')
                
                ->groupBy('typeString', 'idString')
                
                ->get();
    }
    
    
    
    public function attachParticipantAndLastMessage(Collection $messageCollection, $className, $id)
    {
        return $messageCollection->map(function($message) use ($className, $id) {
            $participantClass = $message->typeString;
            
            // Attach Participant
            $message->participant = $participantClass::find($message->idString);
            
            // Attach Last message
            $message->lastMessage = Message::where(function($query) use ($className, $id, $message, $participantClass) {
                           return $query->where('sender_type', $className)
                                        ->where('sender_id', $id)
                                        ->where('receiver_id', $message->participant->id)
                                        ->where('receiver_type', $participantClass); 
                        })
                        
                        ->orWhere(function($query) use ($className, $id, $message, $participantClass) {
                            return $query->where('receiver_type', $className)
                                            ->where('receiver_id', $id)
                                            ->where('sender_id', $message->participant->id)
                                            ->where('sender_type', $participantClass);;
                        })->latest()->first();
            
            // Attach No of unread messages       
            $message->unread = Message::where('read', false)->where(function($query) use ($className, $id, $message, $participantClass) {
                           return $query->where('receiver_type', $className)
                                        ->where('receiver_id', $id)
                                        ->where('sender_id', $message->participant->id)
                                        ->where('sender_type', $participantClass); 
                        })->count();
                        
            return $message;
        });
    }
    
    
    
    public function conversationsWith($model)
    {
        $className = get_class($this);
        $id = $this->id;
        
        return Message::where(function($query) use ($className, $id, $model) {
                           return $query->where('sender_type', $className)
                                        ->where('sender_id', $id)
                                        ->where('receiver_type', get_class($model))
                                        ->where('receiver_id', $model->id); 
                        })
                        
                        ->orWhere(function($query) use ($className, $id, $model) {
                            return $query->where('receiver_type', $className)
                                        ->where('receiver_id', $id)
                                        ->where('sender_type', get_class($model))
                                        ->where('sender_id', $model->id);
                        })->latest()->get();
    }
    
    public function markReceivedMessagesAsRead($model)
    {
        $className = get_class($this);
        $id = $this->id;
        
        return Message::where('read', false)
                        ->where(function($query) use ($className, $id, $model) {
                            return $query->where('receiver_type', $className)
                                        ->where('receiver_id', $id)
                                        ->where('sender_type', get_class($model))
                                        ->where('sender_id', $model->id);
                        })->update(['read' => true]);
    }
    
    
    
    public function getCaseClassName($className)
    {
        return str_replace("\\", "\\\\\\", $className);
    }
}