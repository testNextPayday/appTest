@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.users.index')}}">Users</a></li>
        <li class="breadcrumb-item active">{{$user->reference}}</li>
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
                            User Summary
                            <span class="pull-right">
                                @if($user->is_active)
                                <span class="text-success">Active</span>
                                @else
                                <span class="text-danger">Inactive</span>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="card-group mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-diamond"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$user->loanRequests()->count()}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Loan Request(s)</small>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-speedometer"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$user->receivedLoans()->count()}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Loan(s)</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-layers"></i>
                                        </div>
                                        @if($user->phone_verification && $user->personalProfileIsComplete() && $user->familyProfileIsComplete())
                                        <div class="h4 mb-0 text-success"><i class="icon-check"></i></div>
                                        @else
                                        <div class="h4 mb-0 text-warning"><i class="icon-close"></i></div>
                                        @endif
                                        <small class="text-muted text-uppercase font-weight-bold">Profile Status</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-credit-card"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($user->wallet, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Wallet Balance</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-lock"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($user->escrow, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Escrow Balance</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-4">
                                    {{--
                                    <a href="{{ route('admin.sweep.borrower', ['user' => $user->reference]) }}" class="btn btn-info btn-sm">
                                    Sweep {{$user->name}}'s Loans
                                    </a>
                                    --}}
                                </div>
                                <div class="col-sm-8 text-right">
                                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#salaryNow">
                                  <i class="fa fa-link"></i>Quick Cash Loan Permission
                                  </button>
                                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upgrade">
                                  <i class="fa fa-link"></i>Upgrade User
                                  </button>
                                  <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#refund">
                                  <i class="fa fa-money"></i>Refund
                                  </button>

                                    @if(!$user->staff_id)
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#assignStaff">
                                        <i class="fa fa-link"></i>
                                        Assign Staff
                                    </button>
                                    @else
                                    <a href="{{route('admin.staff.view', ['reference' => App\Models\Staff::find($user->staff_id)->reference])}}" class="btn btn-sm btn-primary">
                                        <i class="icon-eye"></i>
                                        View Staff
                                    </a>
                                    @endif
                                    @if($user->is_active)
                                    <a href="{{route('admin.users.toggle', ['user_id' => encrypt($user->id)])}}" onclick="return confirm('Are you sure you want to disable this user?');" class="btn btn-sm btn-warning"><i class="icon-close"></i> Disable</a>
                                    @else
                                    <a href="{{route('admin.users.toggle', ['user_id' => encrypt($user->id)])}}" onclick="return confirm('Are you sure you want to enable this user?');" class="btn btn-sm btn-success"><i class="icon-check"></i> Enable</a>
                                    @endif
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#topUpWallet">
                                        <i class="fa fa-money"></i>Topup Wallet Ballance
                                    </button>
                                </div>
                            </div>
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
                            <div class="avatar">
                                <img src="{{$user->avatar}}" class="img-avatar" alt="Avatar">
                                <span class="avatar-status badge-success"></span>
                            </div>
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
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <i class="fa fa-user text-success"></i> Personal Info
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    Name: <strong>{{$user->name}}</strong>
                                                </li>
                                                <li class="list-group-item">
                                                    Email: <strong>{{$user->email}}</strong>
                                                </li>
                                                <li class="list-group-item">
                                                    Phone: <strong>{{$user->phone}}</strong>
                                                </li>
                                                <li class="list-group-item">
                                                    Address: <strong>{{$user->address}}</strong>
                                                </li>

                                                <li class="list-group-item">
                                                    LGA: <strong>{{$user->lga}}</strong>
                                                </li>
                                                <li class="list-group-item">
                                                    City, State: <strong>{{$user->city}}, {{$user->state}}</strong>
                                                </li>

                                                <li class="list-group-item dropdown-header text-center">
                                                    <b>DOCUMENTS</b>
                                                </li>
                                                <li class="list-group-item">
                                                    Passport &nbsp;&nbsp;<a target="_blank" href="{{$user->passport}}" class="badge badge-success">view</a>
                                                </li>
                                                <li class="list-group-item">
                                                    Govt. ID &nbsp;&nbsp;<a target="_blank" href="{{$user->govt_id_card}}" class="badge badge-primary">view</a>
                                                </li>
                                            </ul>
                                            <button class="btn mt-2 btn-sm btn-danger text-center" data-toggle="modal" data-target="#update-user-info"> <i class="fa fa-edit"></i> Update</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <i class="fa fa-users text-success"></i> Family Info
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    Marital Status:
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
                                                </li>
                                                <li class="list-group-item">
                                                    No of Children: <strong>{{$user->no_of_children}}</strong>
                                                </li>
                                                <li class="list-group-item">
                                                    Next of Kin: <strong>{{$user->next_of_kin}}</strong>
                                                </li>
                                                <li class="list-group-item">
                                                    Address: <strong>{{$user->next_of_kin_address}}</strong>
                                                </li>

                                                <li class="list-group-item">
                                                    Phone Number: <strong>{{$user->next_of_kin_phone}}</strong>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">

                                </div>
                            </div>

                        </div>
                        <div class="card-footer text-right">

                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            User Employments
                        </div>
                        <div class="card-body">
                            <div class="row">

                                @foreach($user->employments()->latest()->get() as $employment)
                                <div class="col-sm-6 col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            @if($employment->is_current)
                                            <i class="fa fa-briefcase text-success"></i>
                                            @else
                                            <i class="fa fa-briefcase"></i>
                                            @endif
                                            {{$employment->position}} - {{$employment->department}} Department
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    Date Employed: <b>{{$employment->date_employed}}</b>
                                                </li>
                                                <li class="list-group-item">
                                                    Net Salary: <b>{{$employment->net_pay}}</b>
                                                </li>
                                                <!-- <li class="list-group-item">
                                                    Monthly Salary: <b>{{$employment->monthly_salary}}</b>
                                                </li> -->
                                                <li class="list-group-item">
                                                    Supervisor: <b>{{$employment->supervisor_name}}</b>
                                                </li>
                                                <li class="list-group-item">
                                                    MDA: <b>{{$employment->mda}}</b>
                                                </li>
                                                <li class="list-group-item">
                                                    Pay Roll ID : <b>{{$employment->payroll_id}}</b>
                                                </li>
                                                <li class="list-group-item dropdown-header text-center">
                                                    <b>EMPLOYER</b>
                                                </li>
                                                <?php $employer = $employment->employer; ?>
                                                <li class="list-group-item">
                                                    Name: <b>{{$employer->name}}</b>
                                                </li>
                                                <li class="list-group-item">
                                                    Status:
                                                    <b>
                                                        @if($employer->is_verified === 3)
                                                        <span class="text-success">Verified</span>
                                                        @elseif($employer->is_verified === 1)
                                                        <span class="text-info">Undergoing Verification</span>
                                                        @elseif($employer->is_verified === 2)
                                                        <span class="text-danger">Verification Denied</span>
                                                        @else
                                                        <span class="text-warning">Not Verified </span>
                                                        @endif
                                                    </b>
                                                </li>
                                                <li class="list-group-item dropdown-header text-center">
                                                    <b>DOCUMENTS</b>
                                                </li>
                                                <li class="list-group-item">
                                                    Employment Letter &nbsp;&nbsp;<a target="_blank" href="{{$employment->employment_letter}}" class="badge badge-success">view</a>
                                                </li>
                                                <li class="list-group-item">
                                                    Confirmation Letter &nbsp;&nbsp;<a target="_blank" href="{{$employment->confirmation_letter}}" class="badge badge-primary">view</a>
                                                </li>
                                              LOAN RESTRUCTURE SECTION
  <li class="list-group-item">
                                                    Work ID Card &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a target="_blank" href="{{$employment->work_id_card}}" class="badge badge-warning">view</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                        
                                            <employment-update :employers="{{$employers}}" :employment="{{$employment}}" :csrf-token="'{{csrf_token()}}'" :url="'{{route('admin.employments.update', ['employment' => $employment->id])}}'" :is-admin={{true}}></employment-update>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer text-right">

                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Refund logs </h4>
                            <span id="message"></span>
                            <div class="table-responsive mt-3">
                                <table id="order-listing" class="table table-striped table-borderless dt-responsive nowrap default-table m-0 p-0">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Loan</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Reason</th>
                                            <th>Date Created</th>
                                        </tr>
                                    </thead>
                                
                                    <tbody>
                                    @forelse($user->refunds as $index => $refund)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$user->name}}</td>
                                            <td>{{$refund->loanInfo->reference}}</td>
                                            <td>₦ {{number_format($refund->amount)}}</td>
                                            <td>
                                                @if($refund->status == 1)
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($refund->status == 2)
                                                    <span class="badge badge-danger">Rejected</span>
                                                @else

                                                @endif
                                            </td>
                                            <td>{{$refund->reason}}</td>
                                            <td>{{$refund->updated_at}}</td>
                                        </tr>
                                    @empty
                                    <td colspan="6"> No record found</td>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div><!-- end row -->

            <div class="modal fade" id="assignStaff" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Assign staff to user</h4><br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        <form method="post" action="{{route('admin.users.assign-staff')}}">
                            {{csrf_field()}}
                            <div class="modal-body">
                                <input type="hidden" name="type" value="users" />
                                <input type="hidden" name="user_id" value="{{$user->id}}" />
                                <label>Select a staff</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-addon"><i class="icon-question"></i></span>
                                    <select name="staff_id" class="form-control" required>
                                        <?php $validStaff = App\Models\Staff::orderBy('lastname')->get(); ?>
                                        @foreach($validStaff as $staff)
                                        <option value="{{$staff->id}}">{{$staff->lastname}}, {{$staff->firstname}} {{$staff->midname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Assign</button>
                            </div>
                        </form>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div><!-- end modal -->

        <div class="modal fade" id="refund" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Refund user</h4><br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        <form method="post" action="{{route('admin.create.refund')}}">
                            {{csrf_field()}}
                            <div class="modal-body">
                                <input type="hidden" name="type" value="users" />
                                <input type="hidden" name="user_id" value="{{$user->id}}" />
                                <select type="text" class="form-control" name="loan_id" required>
                                   
                                    @php($active_loan = $user->receivedLoans)
                                    @if($active_loan->count() > 0)
                                    @foreach($active_loan as $data)
                                        <option value="{{$data->id}}">{{$data->reference}}(₦{{number_format($data->amount)}})</option>
                                    @endforeach
                                    @else
                                        <option>No loan found</option>
                                    @endif
                                </select>
                                <div class="form-group">
                                    <label>Amount:</label>
                                    <input type="number" name="amount" class="form-control" placeholder="Enter Amount" required>
                                </div>
                                <div class="form-group">
                                    <label>Refund reason:</label>
                                    <textarea type="text" name="reason" class="form-control" placeholder="Summary"></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>
        <!-- Enable Salary Now Loan -->
        <div class="modal fade" id="salaryNow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Quick Cash Loan Permission</h4><br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        <form method="post" action="{{route('admin.enable.salaryloan')}}">
                            {{csrf_field()}}
                            <div class="modal-body">
                                <input type="hidden" name="type" value="users" />
                                <input type="hidden" name="user_id" value="{{$user->id}}" />
                                <select type="text" class="form-control" name="enable_salary_now_loan" required>
                                    <option value="1">Enable</option>                                    
                                    <option value="0">Disable</option>                                    
                                </select>                                
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>
        <!-- End Enable Salary Now Loan -->

        <!-- Topup Wallet Ballance -->
        <div class="modal fade" id="topUpWallet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Topup Wallet Ballance</h4><br>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <form method="post" action="{{route('admin.users.topup_wallet')}}">
                        {{csrf_field()}}
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="hidden" name="user_id" value="{{$user->id}}" />
                            <label for="amount">Amount</label>
                            <input required placeholder="Amount" type="number" name="amount" id="" class="form-control">                        
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
    
                        </div>
                       
                        <div class="modal-footer">
                            <span type="button" class="btn btn-secondary" data-dismiss="modal">Close</span>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
    <!-- End Topup Wallet Ballance -->


        <!-- Upgrade User -->
        <div class="modal fade" id="upgrade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Upgrade User</h4><br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                         @php($levels = [20,40,60,80,100])
                        <form method="post" action="{{route('admin.users.upgrade')}}">
                            {{csrf_field()}}
                            <div class="modal-body">
                                <input type="hidden" name="type" value="users" />
                                <input type="hidden" name="user_id" value="{{$user->id}}"/>
                                <select type="text" class="form-control" name="salary_percentage" required>
                                    @foreach($levels as $index=>$level)
                                        @if($level == $user->salary_percentage)                                            
                                            <option  selected="true" value="{{$level}}">{{$level}}</option>
                                        @else
                                            <option value="{{$level}}">{{$level}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>


            {{-- Edit User info modal --}}

             <!-- Upgrade User -->
        <div class="modal fade" id="update-user-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update User Details</h4><br>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                     @php($levels = [20,40,60,80,100])
                    <form method="post" action="{{route('admin.users.updateInfo')}}">
                        {{csrf_field()}}
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="{{$user->id}}"/>
                            <div class="form-group">
                                <label>Name:</label>
                                <input type="text" name="name" class="form-control" value="{{$user->name}}" required>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <input type="email" name="email" class="form-control" value="{{$user->email}}" required>
                            </div>
                            <div class="form-group">
                                <label>Phone:</label>
                                <input type="text" name="phone" class="form-control" value="{{$user->phone}}">
                            </div>
                            <!-- <div class="form-group">
                                <label>Address:</label>
                                <input type="text" name="address" class="form-control" value="{{$user->address}}">
                            </div>
                            <div class="form-group">
                                <label>LGA:</label>
                                <input type="text" name="lga" class="form-control" value="{{$user->lga}}">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>City:</label>
                                        <input type="text" name="city" class="form-control" value="{{$user->city}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>State:</label>
                                        <input type="text" name="state" class="form-control" value="{{$user->state}}">
                                    </div>
                                </div>
                            </div> -->
                            <div class="form-group">
                                <label>BVN:</label>
                                <input type="text" name="bvn" class="form-control" value="{{$user->bvn}}">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <span type="button" class="btn btn-secondary" data-dismiss="modal">Close</span>
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>



        </div>
        </div>

    </div>
    <!-- /.conainer-fluid -->

</main>
@endsection

@section('page-js')
@endsection