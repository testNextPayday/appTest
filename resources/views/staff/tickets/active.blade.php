@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Active Tickets Needing Support</h4>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">
        @if(count($tickets))
        @foreach($tickets as $ticket)

        <div class="col-md-4 grid-margin stretch-card">
           @component('components.ticket_info', ['ticket'=>$ticket, 'ticket_url'=> route('staff.ticket.show', ['ticket'=>$ticket->reference]), 'close_url'=>route('staff.ticket.close', ['ticket'=>$ticket->reference])])
           @endcomponent
        </div>

        @endforeach
       
        
        @else
        <p class="text-center">No ticket messages available</p>
        @endif
    </div>
</div>

@endsection

@section('page-js')
  
@endsection