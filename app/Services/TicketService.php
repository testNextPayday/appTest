<?php
namespace App\Services;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use Illuminate\Database\Eloquent\Model;
use App\Services\GroupNotificationService;


/**
 * The ticket class handles ticket creation
 */
class TicketService 
{
    /** 
     * @var \App\Services\GroupNotificationService
     */
    public $notifyService;

        
    /**
     * Sets up the notification service when tickets are created or responded to
     *
     * @param  \App\Services\GroupNotificationService $notifyService
     * @return void
     */
    public function __construct(GroupNotificationService $notifyService)
    {
        $this->notifyService = $notifyService;
    }


    /**
     * Creates a ticket with the given data array
     *
     * @param  \Illuminate\Http\Request $request
     * @param array \Illuminate\Database\Eloquent\Model
     * @return void
     */
    public function createTicket(Request $request, Model $model)
    {
       $ticket = $this->saveTicket($request, $model);

       $this->saveMessage($ticket, $model, $request);

        return true;
    }

    
    /**
     * Saves a ticket to the database
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function saveTicket(Request $request, $model)
    {

        return Ticket::create(
            [
                'subject'=> $request->subject,
                'type'=> $request->type,
                'owner_id'=> $model->id,
                'owner_type'=> get_class($model),
                'priority'=> $request->priority
            ]
        );
    }
    
    /**
     * saves replies on a ticket
     *
     * @param   \App\Models\Ticket $ticket
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string $message
     * @return void
     */
    public function saveMessage($ticket, $model, $request)
    {
        $ticketMessage =  [
            'ticket_id'=> $ticket->id,
            'owner_id'=> $model->id,
            'owner_type'=> get_class($model),
            'message'=> $request->message
        ];

        if ($request->hasFile('file') && $request->file('file')->isValid()) {

            $ticketMessage['files'] = $this->uploadMessageDocs($request->file);
        }

        // save initial message
        $message =  TicketMessage::create($ticketMessage);


        $this->handleMessage($message, $model);


        return $message;
    }

    
    /**
     * Carries out other actions after ticket messages has been created
     *
     * @param  mixed $message
     * @param  mixed $model
     * @return void
     */
    protected function handleMessage($message, $model)
    {
        // if this is not a staff  replying send notification
        if (! auth('staff')->check() && ! auth('admin')->check()) {

            $this->notifyService->createNotification($message, $type = 'ticket');

            $data = ['status'=>1];

        } else {

            $data = ['status'=> 2];
           
           // mark all notifications of ticket as read
           $this->setNotificationsAsRead($message->ticket, $model);
        }

        $this->updateTicket($data, $message->ticket);
    }

    
    /**
     * Marks all ticket notifications as read
     *
     * @param  \App\Models\Ticket $ticket
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected function setNotificationsAsRead($ticket, $model)
    {
        
        foreach ($ticket->notifications() as $notification) {

            if ($notification) {

                $this->notifyService->handleNotification($model, $notification->id);
            }
            
        }
    }

    
    /**
     * Upload Files attached to messages
     *
     * @param  mixed $files
     * @return void
     */
    protected function uploadMessageDocs($file)
    {
            
        $images = $file->store('/documents/tickets', 'public');

        return json_encode($images);
    } 

    
    /**
     * Updates tickets
     *
     * @param  array $data
     * @return void
     */
    public function updateTicket(array $data, $ticket)
    {
        $ticket->update($data);
    }

    
    /**
     * Close Ticket
     *
     * @param  mixed $ticket
     * @return void
     */
    public function closeTicket($ticket, $model)
    {
        $this->setNotificationsAsRead($ticket, $model);
        $this->updateTicket(['status'=> 3], $ticket);
    }
}