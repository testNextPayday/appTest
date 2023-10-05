@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Tickets</a></li>
        <li class="breadcrumb-item active">All</li>

        <li class="breadcrumb-menu">
          <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-primary btn-lg waves-effect text-white" href="{{route('users.loan-requests.create')}}" style="border-radius: 20px;"> 
             <span style="font-size: 0.9rem;"> <i class="icon-cursor text-white"></i> Get Loan</span>
            </a>
          </div>
        </li>
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Ticket
                            <span class="pull-right">
                                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#newTicket">New Ticket</button>
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="">
                                    <tr>
                                       
                                        
                                        <th class="text-center">Reference</th>
                                        <th class="text-center">Subject</th>
                                        <th class="text-center">Proiority</th>
                                        <th class="text-center">Last Updated</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                    <tr>
                                        
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                 <a class="badge badge-success text-white p-1" href="{{route('users.ticket.show',['ticket'=> $ticket->reference])}}">{{$ticket->reference}}</a>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$ticket->subject}}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$ticket->priority}}
                                            </div>
                                        </td>
                                        
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$ticket->updated_at->toDateString()}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-center text-muted">
                                                {{$ticket->statusText}}
                                            </div>
                                        </td>

                                        
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            You have not made any Refund request yet
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
    
    <div class="modal fade" id="newTicket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Ticket</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="POST" action="{{route('users.ticket.store') }}" enctype="multipart/form-data">

                <input type="hidden" name="owner_id" value="{{Auth::user()->id}}">
                <input type="hidden" name="owner_type" value="App\Models\User">
                <div class="modal-body">
                
                    <div class="card-body row">
                        {{ csrf_field() }}
                        
                        <div class="form-group col-md-6">
                            <label>Subject </label>
                            <input type="text" class="form-control" name="subject" 
                                
                                placeholder="Enter the subject of the ticket">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Select Type</label>
                            <select name="type" class="form-control">
                                <option value="null">Choose a type of ticket</option>
                                <option value="technical">Technical Ticket</option>
                                <option value="general">General Ticket</option>
                                
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Select proiority</label>
                            <select name="priority" class="form-control">
                                <option value="null">Choose a priority </option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="form-control-label">Message</label>
                            <textarea class="form-control" name="message" rows="10">
                            </textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="attachment">Upload an Attachment</label>
                            <input type="file" class="form-control" name="file">
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</main>
@endsection

@section('page-js')
@endsection