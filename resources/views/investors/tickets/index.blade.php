@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Ticket</h4>
        </div>
    </div>
    <div class="row profile-page">
        <div class="col-12">
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
                                                 <a class="badge badge-success text-white p-1" href="{{route('investors.ticket.show',['ticket'=> $ticket->reference])}}">{{$ticket->reference}}</a>
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
                                                {{$ticket->updated_at}}
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
    </div>
</div>

<div class="modal fade" id="newTicket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Ticket</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="POST" action="{{route('investors.ticket.store') }}" enctype="multipart/form-data">

                <input type="hidden" name="owner_id" value="{{Auth::guard('investor')->user()->id}}">
                <input type="hidden" name="owner_type" value="App\Models\Investor">
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
@endsection