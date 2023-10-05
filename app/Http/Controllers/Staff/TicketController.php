<?php

namespace App\Http\Controllers\Staff;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Services\TicketService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TicketRepliedNotification;

class TicketController extends Controller
{

    protected $service;

        
    /**
     * Sets the service for managing tickets
     *
     * @param  mixed $service
     * @return void
     */
    public function __construct(TicketService $service)
    {
        $this->service = $service;
    }


    /**
     * Display a listing tickets awaiting staff reply
     *
     * @return \Illuminate\Http\Response
     */
    public function active()
    {
        //
        $awaitingReply = Ticket::awaitingStaffReply()->get();
        return view('staff.tickets.active', ['tickets'=> $awaitingReply]);
    }

    
    /**
     * Display converstions of this staff
     *
     * @return void
     */
    public function conversations()
    {
        $staff = Auth::guard('staff')->user();

        $conversations = $staff->ticketReplies;

        return view('staff.tickets.archive', ['conversations'=> $conversations]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function respond(Request $request, Ticket $ticket)
    {
        //
        try {

            $staff = Auth::guard('staff')->user();

            $message = $this->service->saveMessage($ticket, $staff, $request);

            $user = $ticket->owner;

            //$user->notify(new TicketRepliedNotification($message));

        } catch(\Exception $e) {

          
            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Reply successfully sent');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        return view('staff.tickets.show', ['ticket'=>$ticket]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
