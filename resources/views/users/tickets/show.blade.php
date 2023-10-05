@extends('layouts.user')

@section('page-css')
    
@endsection

@section('content')
<!-- Overiding default primary theme -->
<style>
        .btn-primary {
            background-color : #29363d;
        }
</style>

<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">{{$ticket->reference}}</a></li>
        <li class="breadcrumb-item active">All</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            @if ($errors->any())
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-4">
                    @component('components.ticket_info', ['ticket'=>$ticket, 'ticket_url'=> route('users.ticket.show', ['ticket'=>$ticket->reference]), 'close_url'=>route('users.ticket.close', ['ticket'=>$ticket->reference])])
                    @endcomponent
                </div>
                <div class="col-md-8">
                    @if ($ticket->status != 3)
                        <!-- Ticket Reply Form -->
                        @component('components.ticket_reply_form', ['url'=> route('users.ticket.respond', ['ticket'=> $ticket->reference]), 'user'=> Auth::user()])
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
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
   

</main>
@endsection

@section('page-js')
@endsection