<div class="card">
                        <div class="card-header">
                            <span class="btn btn-outline-primary float-right"><a  href="{{$ticket_url}}">View Ticket</a></span>
                            <h5>Ticket Information</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{$ticket->subject}}
                                    <span class="badge badge-primary badge-pill">{{$ticket->reference}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Status
                                    <span class="badge badge-black badge-pill">{{$ticket->statusText}}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Type
                                    <span class="badge badge-primary badge-pill">{{ucfirst($ticket->type)}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Priority
                                    <span class="badge badge-primary badge-pill">{{ucfirst($ticket->priority)}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                     Date Created
                                    <span class="badge badge-primary badge-pill">{{$ticket->created_at}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Last Updated
                                    <span class="badge badge-primary badge-pill">{{$ticket->updated_at}}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="card-footer text-center">
                          @if (! auth('staff')->user())
                            <form method="POST" action="{{$close_url}}">
                                {{csrf_field()}}
                                <button class="btn btn-outline-danger" type="submit">Close Ticket</button>
                            </form>
                        @endif
                        </div>
                    </div>