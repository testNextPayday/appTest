<?php

namespace App\Http\Controllers\Users;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Services\TicketService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    protected $service;

    /**
     * Injects the ticket service into the class for creating tickets
     *
     * @param  mixed $service
     * @return void
     */
    public function __construct(TicketService $service)
    {
       
        $this->service = $service;
    }

    
    /**
     * Display user tickets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user  = Auth::user();
        $tickets  = $user->tickets->sortByDesc('created_at');

        return view('users.tickets.index', ['tickets'=>$tickets]);
    }

    /**
     * Show the form for creating a new ticket.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $this->service->createTicket($request, $user = Auth::user());

        }catch (\Exception $e) {
           
            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Ticket created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Ticket $ticket)
    {
        //
        return view('users.tickets.show', ['ticket'=> $ticket]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function close(Ticket $ticket)
    {
        //
        try {
            $user = Auth::user();
            $this->service->closeTicket($ticket, $user);
            
        } catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Ticket has been closed');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function respond(Request $request, Ticket $ticket)
    {
        //
        try {

            $user = Auth::user();

            $this->service->saveMessage($ticket, $user, $request);

        }catch (\Exception $e) {

            return redirect()->back()->with('failure', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Reply successfully sent');
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
