<?php

namespace App\Http\Controllers\Investors;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Services\TicketService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    protected $service;

    
    /**
     * Injects the service
     *
     * @param  mixed $service
     * @return void
     */
    public function __construct(TicketService $service)
    {
       
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user  = Auth::guard('investor')->user();
        $tickets  = $user->tickets;

        return view('investors.tickets.index', ['tickets'=>$tickets]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('investors.tickets.create');
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
           
            $user = Auth::guard('investor')->user();
            
            $this->service->createTicket($request, $user);

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
    public function show(Ticket $ticket)
    {
        //
        return view('investors.tickets.show', ['ticket'=>$ticket]);
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
            $investor = Auth::guard('investor')->user();
            $this->service->closeTicket($ticket, $investor);
            
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

            $investor = Auth::guard('investor')->user();

            $this->service->saveMessage($ticket, $investor, $request);

        } catch(\Exception $e) {

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
