@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">View Ticket {{$ticket->reference}}</h4>
        </div>
    </div>
    <div class="row">
                <div class="col-md-4">
                    @component('components.ticket_info', ['ticket'=>$ticket, 'ticket_url'=> route('investors.ticket.show', ['ticket'=>$ticket->reference]), 'close_url'=>route('investors.ticket.close', ['ticket'=>$ticket->reference])])
                    @endcomponent
                </div>
                <div class="col-md-8">
                    @if ($ticket->status != 3)
                        <!-- Ticket Reply Form -->
                        @component('components.ticket_reply_form', ['url'=> route('investors.ticket.respond', ['ticket'=> $ticket->reference]), 'user'=> Auth::guard('investor')->user()])
                        @endcomponent
                    @endif

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