@extends('layouts.admin')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.loan-requests.pending')}}">Pending Loan Requests</a></li>
        <li class="breadcrumb-item active">{{$loanRequest->reference}}</li>
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
                            Loan Request Details
                            <span class="pull-right">
                                @switch($loanRequest->status)
                                    @case(0)
                                        <span class="text-danger">Inactive - Waiting for employer approval</span>
                                        @break
                                    @case(1)
                                        <span class="text-warning">Pending Admin Approval</span>
                                        @break
                                    @case(2)
                                        <span class="text-success">Active</span>
                                        @break
                                    @case(3)
                                        <span class="text-danger">Cancelled</span>
                                        @break
                                    @case(4)
                                        <span class="text-success">Taken (Closed out)</span>
                                        @break
                                    @case(5)
                                        <span class="text-danger">Declined - Employer</span>
                                        @break
                                    @case(6)
                                    @default
                                        <span class="text-danger">Declined - Admin</span>
                                @endswitch
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="card-group mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-diamond"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($loanRequest->amount, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Amount</small>
                                        <?php 
                                            $insurance = 2.5/100 * $loanRequest->amount;
                                        ?>
                                        <small class="text-muted text-uppercase font-weight-bold">INSURANCE: ₦{{number_format($insurance, 2)}}</small>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-pie-chart"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loanRequest->interest_percentage}} %</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Interest</small>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-target"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loanRequest->emi()}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">EMI</small>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-speedometer"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loanRequest->duration}} Months</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Duration</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-layers"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($loanRequest->funds()->sum('amount'), 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Realized</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-compass"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loanRequest->percentage_left}} %</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Percentage Left</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-calendar"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loanRequest->expected_withdrawal_date}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Date Expected</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-calendar"></i>
                                        </div>
                                        <div class="h4 mb-0"><a href="{{$loanRequest->bank_statement}}" target="_blank">View</a></div>
                                        <small class="text-muted text-uppercase font-weight-bold">Bank Statement</small>
                                    </div>
                                </div>
            
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            @if($loanRequest->status == 0)
                            <button type="button" class="btn btn-danger">Pending Employer Approval</button>
                            @if($loanRequest->status == 1)
                            <a class="btn btn-success">
                                Approve Request
                            </a>
                            <a onclick="return confirm('Are you sure you want to reject this loan?');" 
                                class="btn btn-outline-danger"
                                href="{{route('users.loan-requests.funds.reject', ['reference' => $loanRequest->reference])}}">
                                Decline Request
                            </a>
                            <button type="button" class="btn btn-warning">Pending Admin Activation</button>
                            @elseif($loanRequest->status == 2)
                            <button type="button" class="btn btn-success">Active</button>
                            @elseif($loanRequest->status == 3)
                            <button type="button" class="btn btn-default">Cancelled</button>
                            @elseif($loanRequest->status == 4)
                            <button type="button" class="btn btn-success">Taken</button>
                            <a href="{{route('users.loans.view', ['reference' => $loanRequest->loan->reference])}}" class="btn btn-primary">View Loan</a>
                            @elseif($loanRequest->status == 5)
                            <button type="button" class="btn btn-danger">Declined - Employer</span>
                            @elseif($loanRequest->status == 6)
                            <button type="button" class="btn btn-danger">Declined - Admin</span>
                            @endif
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            User Details
                            <span class="pull-right">
                                @if($user->is_active)
                                <span class="text-success">Active</span>
                                @else
                                <span class="text-danger">Inactive</span>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-2">
                                    <img src="{{$user->avatar}}" style="width:100%; border: 1px solid #000; border-radius: 5%"/>
                                </div>
                                <div class="col-sm-4">
                                    <p>Name: <strong>{{$user->lastname}}, {{$user->firstname}} {{$user->midname}}</strong></p>
                                    <p>Email: <strong>{{$user->email}}</strong></p>
                                    <p>Phone: <strong>{{$user->phone}}</strong></p>
                                    <p>Address: <strong>{{$user->address}}</strong></p>
                                    <p>LGA: <strong>{{$user->lga}}</strong></p>
                                    <p>City, State: <strong>{{$user->city}}, {{$user->state}}</strong></p>
                                </div>
                                <div class="col-sm-4">
                                    <p>Marital Status: 
                                        <strong>
                                            @switch($user->marital_status)
                                                @case(1)
                                                Single
                                                @break
                                                @case(2)
                                                Married
                                                @break
                                                @case(3)
                                                Divorced
                                                @break
                                                @case(4)
                                                Widowed
                                                @break
                                                @default
                                                N/A
                                            @endswitch
                                        </strong>
                                    </p>
                                    <p>No of Children: <strong>{{$user->no_of_children}}</strong></p>
                                    <p>Next of Kin: <strong>{{$user->next_of_kin}}</strong></p>
                                    <p>Address: <strong>{{$user->next_of_kin_address}}</strong></p>
                                    <p>Phone Number: <strong>{{$user->next_of_kin_phone}}</strong></p>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="text-uppercase text-primary bold">Employments</h4>
                                </div>
                                <hr/>
                                <div class="col-sm-12">
                                    <div class="card-group mb-4">
                                        @foreach($user->employments()->latest()->get() as $employment)
                                        <div class="card">
                                            <div class="card-header">
                                                <h5><small class="text-muted text-uppercase font-weight-bold">{{$employment->position}}</small></h5>
                                            </div>
                                            <div class="card-body">
                                                <!-- <h5><small class="text-muted text-uppercase font-weight-bold">Monthly Salary:</small> //{{$employment->monthly_salary}}</h5> -->
                                                <h5><small class="text-muted text-uppercase font-weight-bold">Date Employed:</small> {{$employment->date_employed}}</h5>
                                                <h5><small class="text-muted text-uppercase font-weight-bold">Date Confirmed:</small> {{$employment->date_confirmed}}</h5>
                                                <h5><small class="text-muted text-uppercase font-weight-bold">Employer:</small> {{$employment->employer->name}}</h5>
                                                <h5><small class="text-muted text-uppercase font-weight-bold">Employer Email:</small> {{$employment->employer->email}}</h5>
                                                <h5><small class="text-muted text-uppercase font-weight-bold">Employer Phone:</small> {{$employment->employer->phone}}</h5>
                                                <h5><small class="text-muted text-uppercase font-weight-bold">Employer Address:</small> {{$employment->employer->address}}</h5>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            
            
            @if($loanRequest->status > 1)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Funds for this Request ({{$loanRequest->funds()->count()}})
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-people"></i></th>
                                        <th>Funder</th>
                                        <th class="text-center">Offer</th>
                                        <th>Fund Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loanRequest->funds()->latest()->get() as $fund)
                                    <tr>
                                        <td class="text-center">
                                            <div class="avatar">
                                                <img src="{{$fund->lender->avatar}}" class="img-avatar" alt="avatar">
                                                <span class="avatar-status badge-success"></span>
                                             </div>
                                        </td>
                                        <td>
                                            <div>{{$fund->lender->lastname}} {{$fund->lender->firstname}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                                ₦ {{$fund->amount}} <span>({{$fund->percentage}}%)</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$fund->created_at}}
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            There are no funds for this Request
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
            @endif
            <!--/.row-->
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Previous Loan Requests
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Interest %</th>
                                        <th class="text-center">Request Date</th>
                                        <th>Status</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($loanRequests))
                                    @forelse($loanRequests as $loanRequest)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$loanRequest->reference}}</div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                               ₦ {{$loanRequest->amount}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$loanRequest->interest_percentage}}
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                {{$loanRequest->created_at}}
                                            </div>
                                        </td>
                                        <td>
                                            @switch($loanRequest->status)
                                                @case(0)
                                                    <span class="text-danger">Inactive - Waiting for employer approval</span>
                                                    @break
                                                @case(1)
                                                    <span class="text-warning">Pending Admin Approval</span>
                                                    @break
                                                @case(2)
                                                    <span class="text-success">Active</span>
                                                    @break
                                                @case(3)
                                                    <span class="text-danger">Cancelled</span>
                                                    @break
                                                @case(4)
                                                    <span class="text-success">Taken (Closed out)</span>
                                                    @break
                                                @case(5)
                                                    <span class="text-danger">Declined - Employer</span>
                                                    @break
                                                @case(6)
                                                @default
                                                    <span class="text-danger">Declined - Admin</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <a class="label label-warning"><i class="icon-eyes"></i> View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            {{$loanRequests->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            This user has no previous loan request
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
        </div>

    </div>
    <!-- /.conainer-fluid -->
    
    <div class="modal fade" id="approveRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-primary" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Approve Loan Request: {{$loanRequest->reference}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="POST" action="{{route('admin.loan-requests.approve')}}">
                <div class="modal-body">
                    <div class="card-body">
                        {{ csrf_field() }}
                        <input type="hidden" name="request_id" value="{{$loanRequest->id}}"/>
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
    <!-- /.modal -->
</main>
@endsection

@section('page-js')
@endsection