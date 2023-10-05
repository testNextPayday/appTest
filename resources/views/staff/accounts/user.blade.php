@extends('layouts.staff-new')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                Account {{$user->reference}}
            </h4>
        </div>
    </div>
    
    <div class="row profile-page">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="profile-header text-white">
                        <div class="d-flex justify-content-around">
                            <div class="profile-info d-flex justify-content-center align-items-md-center">
                                <img class="rounded-circle img-lg" src="{{ $user->avatar }}" alt="profile image">
                                <div class="wrapper pl-4">
                                    <p class="profile-user-name">{{ $user->name}}</p>
                                    <div class="wrapper d-flex align-items-center">
                                        <p class="profile-user-designation">{{ strtoupper($user->reference) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="details d-none d-md-block">
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-body pt-0 pt-sm-4">
                        
                        @include('layouts.shared.error-display')
                        
                        <ul class="nav tab-switch " role="tablist ">
                            <li class="nav-item ">
                                <a class="nav-link active " id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info"
                                    role="tab " aria-controls="user-profile-info" aria-selected="true ">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " id="user-employments-tab" data-toggle="pill" href="#user-employments"
                                    role="tab " aria-controls="user-employments" aria-selected="false ">Employments</a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link " id="user-refund-tab" data-toggle="pill" href="#user-refund"
                                    role="tab " aria-controls="user-refund" aria-selected="false ">Create Refunds <small>(Available : ₦{{number_format($user->masked_loan_wallet, 2)}})</small></a>
                            </li>
                            @php($staff = auth('staff')->user())
                            @if ($staff->manages('upgrade_user'))
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upgrade">
                                  <i class="fa fa-link"></i>Upgrade User
                            </button>
                            @endif
                        </ul>
                        
                        <div class="row ">
                       
                            <div class="col-12 col-md-9">
                            
                                <div class="tab-content tab-body" id="profile-log-switch">
                                    <div class="tab-pane fade show active pr-3 " id="user-profile-info" role="tabpanel"
                                        aria-labelledby="user-profile-info-tab">
                                        
                                        <div class="row ">
                                            <div class="col-12 mt-5">
                                                <h5>Basic Information</h5>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive ">
                                            <table class="table table-borderless w-100 mt-4 ">
                                                <tr>
                                                    <td>
                                                        <strong>Full Name :</strong> {{ $user->name }}
                                                    </td>
                                                    <td>
                                                        <strong>Phone :</strong> {{ $user->phone }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Email :</strong> {{ $user->email }}
                                                    </td>
                                                    <td>
                                                        <strong>Address :</strong> {{ $user->address }}                                                    
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>LGA :</strong> {{ $user->lga }}
                                                    </td>
                                                    <td>
                                                        <strong>City, State :</strong> {{ $user->city }}, {{$user->state}}                                                    
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        
                                        <div class="row ">
                                            <div class="col-12 mt-5">
                                                <h5 class="mb-5 ">Other Information</h5>
                                                <div class="stage-wrapper pl-4">
                                                    <div class="stages border-left pl-5 pb-4">
                                                        <div class="btn btn-icons btn-rounded stage-badge btn-inverse-primary">
                                                              <i class="icon-people"></i>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-2 justify-content-between">
                                                          <h5 class="mb-0 ">Family Data</h5>
                                                        </div>
                                                        <p><strong>Marital Status:</strong> 
                                                            @component('components.marital-status', ['status' => $user->marital_status])
                                                            @endcomponent
                                                        </p>
                                                        <p><strong>No. of Children:</strong> {{ $user->no_of_children }}</p>
                                                        <p><strong>Next of Kin:</strong> {{ $user->next_of_kin }}</p>
                                                        <p><strong>Next of Kin's Address:</strong> {{ $user->next_of_kin_address }}</p>
                                                        <p><strong>Next of Kin's Phone:</strong> {{ $user->next_of_kin_phone }}</p>
                                                    </div>
                                                    <div class="stages border-left pl-5 pb-4">
                                                        <div class="btn btn-icons btn-rounded stage-badge btn-inverse-success">
                                                              <i class="mdi mdi-texture "></i>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-2 justify-content-between">
                                                          <h5 class="mb-0 ">Bank Details</h5>
                                                        </div>
                                                        @if($user->bank)
                                                        <p>Bank Name: {{ $user->bank->bank_name }}</p>
                                                        <p>Bank Code: {{ $user->bank->bank_code }}</p>
                                                        <p>Account Number: {{ $user->bank->account_number }}</p>
                                                        @else
                                                        Bank details unavailable
                                                        @endif
                                                    </div>
                                                    <div class="stages pl-5 pb-4">
                                                        <div class="btn btn-icons btn-rounded stage-badge btn-inverse-primary">
                                                            <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-2 justify-content-between">
                                                            <h5 class="mb-0">Phone Verification</h5>
                                                        </div>
                                                        <p>Verify your phone number.</p>
                                                        <p><em>* This service is currently unavailable</em></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="user-employments" role="tabpanel"
                                        aria-labelledby="user-employments-tab">
                                        <div class="row ">
                                            <div class="row ">
                                                <div class="col-12 mt-5">
                                                    <h5 class="">User Employments</h5>
                                                    <p class="mb-5">List of user's employments</p>
                                                    <div class="stage-wrapper pl-4">
                                                        @forelse($user->employments()->latest()->get() as $employment)
                                                        <div class="stages border-left pl-5 pb-4">
                                                            <div class="btn btn-icons btn-rounded stage-badge btn-inverse-primary">
                                                                  <!--<i class="icon-people"></i>-->
                                                                  {{$loop->iteration}}
                                                            </div>
                                                            <div class="d-flex align-items-center mb-2 justify-content-between">
                                                              <h3 class="mt-2 ">
                                                                  {{ $employment->position }}&nbsp;
                                                                  <employment-update :employment="{{$employment}}"
                                                                    :csrf-token="'{{csrf_token()}}'"
                                                                    :url="'{{route('staff.employments.update', ['employment' => $employment->id])}}'"></employment-update>
                                                              </h3>
                                                            </div>
                                                            <br/>
                                                            <p><strong>Net Salary:</strong> ₦ {{number_format($employment->net_pay, 0)}}</p>
                                                            <p><strong>Date Employed:</strong> {{$employment->date_employed}}</p>
                                                            <p><strong>Date Confirmed:</strong> {{$employment->date_confirmed}}</p>
                                                            <p><strong>MDA:</strong> {{$employment->mda}}</p>
                                                            <p><strong>PayRoll ID:</strong> {{$employment->payroll_id}}  <span style="font-size:20px;cursor:pointer" onclick="document.getElementById('payroll_form').classList.toggle('display-none')"> <i class="fa fa-edit"></i> </span> </p> 
                                                            <div id="payroll_form" class="display-none">
                                                                <form action="{{route('staff.employments.update_payroll',['employment'=>$employment->id])}}" method="POST">
                                                                    @csrf
                                                                    <div class="form-group">
                                                                        <label class="form-control-label">PayRoll ID</label>
                                                                        <input type="text" name="payroll_id" class="form-control" value="{{$employment->payroll_id}}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="submit" name="submit" class="btn btn-primary" value="save">
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <p><strong>Employer:</strong> {{$employment->employer->name}}</p>
                                                            <p><strong>Employer Email:</strong> {{$employment->employer->email}}</p>
                                                            <p><strong>Employer Phone:</strong> {{$employment->employer->phone}}</p>
                                                            <p><strong>Employer Address:</strong> {{$employment->employer->address}}</p>
                                                            
                                                            <hr/>
                                                            <p>
                                                                <strong>Documents</strong>
                                                                @if($employment->getOriginal('employment_letter'))
                                                                    &nbsp;&nbsp;
                                                                    <a href="{{ $employment->employment_letter }}" target="_blank"
                                                                        class="btn btn-xs btn-outline-info">
                                                                        Employment Letter
                                                                    </a>
                                                                @endif
                                                                @if($employment->getOriginal('confirmation_letter'))
                                                                    &nbsp;&nbsp;
                                                                    <a href="{{ $employment->confirmation_letter }}" target="_blank"
                                                                        class="btn btn-xs btn-outline-info">
                                                                        Confirmation Letter
                                                                    </a>
                                                                @endif
                                                                @if($employment->getOriginal('work_id_card'))
                                                                    &nbsp;&nbsp;
                                                                    <a href="{{ $employment->work_id_card }}" target="_blank"
                                                                        class="btn btn-xs btn-outline-info">
                                                                        Work ID
                                                                    </a>
                                                                @endif
                                                                
                                                            
                                                            </p>
                                                            <hr/>
                                                        </div>
                                                        @empty
                                                            <div class="stages border-left pl-5 pb-4">
                                                                <div class="btn btn-icons btn-rounded stage-badge btn-inverse-success">
                                                                      <i class="mdi mdi-texture "></i>
                                                                </div>
                                                                <div class="d-flex align-items-center mb-2 justify-content-between">
                                                                  <h5 class="mb-0 ">No employments</h5>
                                                                </div>
                                                                Employments unavailable
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="tab-pane fade" id="user-refund" role="tabpanel"
                                        aria-labelledby="user-refund-info-tab">
                                        <div class="row">
                                            <div class="col-md-9">
                                                
                                                <form method="post" action="{{route('staff.create.refund')}}">
                                                    {{csrf_field()}}
                                                    <input type="hidden" name="user_id" value="{{$user->id}}" />
                                                    <input type="hidden" name="staff_id" value="{{auth()->user()->id}}"/>
                                                    <div class="form-group">
                                                        <label for="loan">User Loans:</label>
                                                        <select class="form-control" type="text" name="loan_id" id="loan">
                                                               
                                                                <?php $active_loan = $user->receivedLoans; ?>
                                                                @if(count($active_loan) > 0)
                                                                @foreach($active_loan as $data)
                                                                    <option value="{{$data->id}}">{{$data->reference}}(₦{{number_format($data->amount)}})</option>
                                                                @endforeach
                                                                @else
                                                                    <option>No loan found</option>
                                                                @endif
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="amount">Amount:</label>
                                                        <input class="form-control" type="text" name="amount" id="amount"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="reason">Reason:</label>
                                                        <textarea class="form-control" type="text" name="reason" id="reason">
                                                        </textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="btn btn-success btn-block">Create refund</button>
                                                    </div>
                                                </form>
                                            </div>

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
                                                                        <td>{{$refund->getUSerInfo->name}}</td>
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
                                        </div>
                                    </div>
                                    <div class="modal fade" id="upgrade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    @if ($staff->manages('upgrade_user'))
                                    <div class="modal-dialog modal-sm" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Upgrade User</h4><br>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            @php($levels = [20,40,60,80,100])
                                            <form method="post" action="{{route('staff.users.upgrade')}}">
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
                                    @endif
                            </div>
                           </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection

@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection