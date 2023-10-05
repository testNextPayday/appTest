@php

    $declineRoute = route($prefix.'.loan-requests.decline', ['reference' => $loanRequest->reference]);
    $referRoute = route($prefix.'.loan-requests.refer', ['reference' => $loanRequest->reference]);
    $updateRoute = route($prefix.'.loan-requests.update');
    $approveRoute = route($prefix.'.loan-requests.approve');
    $assignRoute = route($prefix.'.loan-requests.assign', ['reference'=> $loanRequest->reference]);
    $unassignRoute = route($prefix.'.loan-requests.unassign', ['reference'=> $loanRequest->reference]);

    $user = $loanRequest->user;

    $staff = auth('staff')->user();

    $admin = auth('admin')->user();

@endphp

@if($loanRequest->status == 1 || $loanRequest->status == 0)
<div class="d-flex">
<!-- <a data-toggle="modal" data-target="#availableOkraBanks" class="btn btn-outline-success">
    View Available Okra Banks
</a> -->
<a data-toggle="modal" data-target="#approveRequestModal" class="btn btn-outline-success">
    Approve Request
</a>

<a data-toggle="modal" data-target="#declineRequestModal" class="btn btn-outline-danger">
    Decline Request
</a>

@if($admin || $staff->manages('edit_loan_request'))
<a class="btn btn-outline-warning" data-toggle="modal" data-target="#edit_loan_request_modal">Edit Request</a>
@endif

@if ($admin || $staff->manages('assign_loan_request'))
<a class="btn btn-outline-warning" data-toggle="modal" data-target="#assignRequestModal">Assign Request</a>
@endif

<a class="btn btn-outline-warning" data-toggle="modal" data-target="#referRequestModal">Refer Request</a>


@if(!$user->bvnVerified())
<a class="btn btn-outline-primary" data-toggle="modal" data-target="#bvnRequestModal">Verify BVN</a>
@endif

<a class="btn btn-outline-danger" data-toggle="modal" data-target="#resolveAccountModal">Resolve Bank Account</a>
<a class="btn btn-outline-danger" data-toggle="modal" data-target="#resolveCardModal">Resolve Card Details</a>
</div>

@if($admin || $staff->manages('edit_loan_request'))
@include('components.modals.edit_lr', ['url'=>$updateRoute])
@endif

@if($loanRequest->status == 0)
<br />
<em><small><b>NB: This action will override employer consent</b></small></em>
@endif
@elseif($loanRequest->status == 2)
<button type="button" class="btn btn-success">Active</button>
@elseif($loanRequest->status == 3)
<button type="button" class="btn btn-default">Cancelled</button>
@elseif($loanRequest->status == 4)
<button type="button" class="btn btn-success">Taken</button>
@if($loanRequest->loan)

    @if(auth('staff')->check())
        <a href="{{route('staff.loans.view', ['reference' => $loanRequest->loan->reference])}}" class="btn btn-primary">View Loan</a>
    @endif

    @if(auth('admin')->check())
        <a href="{{route('admin.loans.view', ['reference' => $loanRequest->loan->reference])}}" class="btn btn-primary">View Loan</a>
    @endif
@else
<a class="btn btn-primary" href="{{route('admin.loan-requests.view', ['reference' => $loanRequest->reference])}}">
    Awaiting Setup
</a>
<!-- <button type="button" class="btn btn-primary">Awaiting Setup</button> -->
@endif
@elseif($loanRequest->status == 5)
<button type="button" class="btn btn-danger">Declined - Employer</button>
@elseif($loanRequest->status == 6)
<button type="button" class="btn btn-danger">Declined - Admin</button>
@else 
<a data-toggle="modal" data-target="#declineRequestModal" class="btn btn-outline-danger">
    Decline Request
</a>
@endif

<div class="modal fade" id="bvnRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Verify BVN: {{$loanRequest->reference}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
           
            <div class="modal-body">
                @if($user->bankDetails->last())
                    <div class="col-sm-6" >
                        <bvn-verificator :user="{{$user}}" :bank_record="{{$user->bankDetails->last()}}"></bvn-verificator>
                    </div>
                @else
                    <p class="text-danger">No bank account present so cannot verify BVN</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
               
            </div>
           
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="approveRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Approve Loan Request: {{$loanRequest->reference}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{$approveRoute}}">
            <div class="modal-body">
                <div class="card-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="request_id" value="{{$loanRequest->id}}"/>
                    <input type="hidden" name="collection_plan" value="0"/>
                    <div class="form-group">
                        <label for="risk_rating">Select Risk Rating</label>
                        <select class="form-control" id="risk_rating" name="risk_rating" required>
                            <option value="5">5</option>
                            <option value="4">4</option>
                            <option value="3">3</option>
                            <option value="2">2</option>
                            <option value="1">1</option>
                        </select> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Approve Loan Request</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="availableOkraBanks" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Available Okra Banks</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
           
            <div class="modal-body">
                <div class="card-body">
                    <table class="row">                                            
                        @php($banks = Config::get('okra.banks'))                  
                        @foreach($banks as $bank)
                            <tr> <td>{{$bank}} </td></tr>
                        @endforeach
                    </table>                   
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>               
            </div>            
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@if ($admin || $staff->manages('assign_loan_request'))
<div class="modal fade" id="assignRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Assign Loan Request: {{$loanRequest->reference}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{$assignRoute}}">
            <div class="modal-body">
                <div class="card-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="reference" value="{{$loanRequest->reference}}"/>
                    <div class="form-group">
                        @if($placer = $loanRequest->placer)
                        <label>Currently Assigned : {{$placer->name}} ({{$placer->reference}})</label> 
                        @endif
                        
                    </div>
                    <div class="form-group">
                        <label for="affiliate_id">Choose Affiliate</label>
                        <select class="form-control" id="affiliate_id" name="affiliate_id" required>
                            @php($affiliates = \App\Models\Affiliate::active()->get())
                            <option value="null">Select Affiliate</option>
                            @foreach($affiliates as $affiliate)
                                
                                 <option value="{{$affiliate->id}}" {{ $loanRequest->placer_id == $affiliate->id ? 'selected="selected"':''}} >{{$affiliate->name}}</option>
                               
                               

                            @endforeach
                        </select> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if($loanRequest->placer_id)
                    <button type="button" class="btn btn-danger" onclick="event.preventDefault();document.getElementById('unassignForm').submit();">Unassign Loan Request</button>
                @endif
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-primary">Assign Loan Request</button>
            </div>
            </form>

            <form action="{{$unassignRoute}}" method="POST" id="unassignForm">
                    {{ csrf_field() }}
                    <input type="hidden" name="reference" value="{{$loanRequest->reference}}"/>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endif

<div class="modal fade" id="declineRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Decline Loan Request: {{$loanRequest->reference}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="GET" action="{{$declineRoute}}">
            <div class="modal-body">
                <div class="card-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="reference" value="{{$loanRequest->reference}}"/>
                   
                    <div class="form-group">
                        <label for="decline_reason">Enter Reason</label>
                        <input type="text" class="form-control" name="decline_reason">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
               
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-danger">Decline Loan Request</button>
            </div>
            </form>

          
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<div class="modal fade" id="referRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Refer Loan Request: {{$loanRequest->reference}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="GET" action="{{$referRoute}}">
            <div class="modal-body">
                <div class="card-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="reference" value="{{$loanRequest->reference}}"/>
                   
                    <div class="form-group">
                        <label for="refer_reason">Enter Reason</label>
                        <input type="text" class="form-control" name="refer_reason">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
               
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-danger">Refer Loan Request</button>
            </div>
            </form>

          
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<div class="modal fade" id="resolveAccountModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Refer Loan Request: {{$loanRequest->reference}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            <div class="modal-body">
                @if ($user->bankDetails->last())
                    <resolve-account :bankdetails="{{$user->bankDetails->last()}}" :banks="{{json_encode(config('remita.banks'))}}"></resolve-account>
                @else
                    <span class="text-danger">No Bank detail found on this account</span>
                @endif 
            </div>

          
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="resolveCardModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Refer Loan Request: {{$loanRequest->reference}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            <div class="modal-body">
                @if ($user->billingCards->last())
                    <resolve-card :user_reference="'{{$user->reference}}'"></resolve-card>
                @else
                    <span class="text-danger">No Card details found on this account</span>
                @endif 
            </div>

          
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

