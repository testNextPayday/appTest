@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">View Ticket</h4>
        </div>
    </div>
    
    <div class="row">
                <div class="col-md-4">
                    @component('components.ticket_info', ['ticket'=>$ticket, 'ticket_url'=> route('staff.ticket.show', ['ticket'=>$ticket->reference])])
                    @endcomponent
                </div>
                <div class="col-md-8">

                    <!-- Ticket Reply Form -->
                    @component('components.ticket_reply_form', ['url'=> route('staff.ticket.respond', ['ticket'=> $ticket->reference]), 'user'=> Auth::guard('staff')->user()])
                    @endcomponent

                    <!-- Ticket Reply Form Ends Here -->

                    <!-- Conversation Contents -->
                    <br>
                    <br>
                    <div id="conversations">
                    
                        @forelse($ticket->messages->sortByDesc('created_at') as $conversation)
                            @component('components.ticket_message', ['conversation'=> $conversation])
                            @endcomponent
                        @empty
                            <p>No conversation has been found on this ticket</p>
                        @endforelse
                    </div>

                    <!-- Conversation Contents ends here -->


                </div>
                <!--/.col-->
            </div>
</div>

@endsection

@section('page-js')
  
@endsection