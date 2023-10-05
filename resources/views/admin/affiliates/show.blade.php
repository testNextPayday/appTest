@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                {{ $affiliate->name }} ({{ $affiliate->reference }}) &nbsp;
                @if ($affiliate->verified_at)
                    @if($affiliate->status)
                        <span class="badge badge-success">
                            <i class="fa fa-check-circle-o"></i>
                        </span>
                    @else
                        <span class="badge badge-warning">
                            <i class="fa fa-lock"></i>
                        </span>
                    @endif
                @else
                    <span class="badge badge-danger">
                        <i class="fa fa-close"></i>
                    </span>
                @endif
            </h4>
        </div>
    </div>
    
    @component('components.affiliate-statistics', ['affiliate' => $affiliate])
    @endcomponent
    
    <div class="row profile-page">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="profile-header text-white d-none d-md-block">
                        <div class="d-flex justify-content-around">
                            <div class="profile-info d-flex justify-content-center align-items-md-center">
                                <img class="rounded-circle img-lg" src="{{ $affiliate->avatar }}" alt="profile image">
                                <div class="wrapper pl-4">
                                    <p class="profile-user-name">
                                        {{ $affiliate->name}}
                                    </p>
                                    <div class="wrapper d-flex align-items-center">
                                        <p class="profile-user-designation">Nextpayday Agent</p>
                                    </div>
                                </div>
                            </div>
                            <div class="details">
                                <p>{{ $affiliate->referralLink }}</p>
                            </div>
                        </div>
                    </div>
                  
                  
                    <div class="profile-body pt-0 pt-sm-4">
                        @include('layouts.shared.error-display')
                        
                        <ul class="nav tab-switch " role="tablist ">
                            <li class="nav-item ">
                                <a class="nav-link active " id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info"
                                    role="tab " aria-controls="user-profile-info" aria-selected="true ">Basic Information</a>
                            </li>
                            <!--<li class="nav-item ">-->
                            <!--    <a class="nav-link " id="user-profile-activity-tab" data-toggle="pill" href="#user-profile-activity"-->
                            <!--        role="tab " aria-controls="user-profile-activity" aria-selected="false ">Update Information</a>-->
                            <!--</li>-->
                        </ul>
                        <div class="row ">
                            <div class="col-12 col-md-9">
                                <div class="tab-content tab-body" id="profile-log-switch ">
                                    <div class="tab-pane fade show active pr-3 " id="user-profile-info" role="tabpanel"
                                        aria-labelledby="user-profile-info-tab">
                                        
                                        <div class="row">
                                            <!-- Manage user status -->
                                            <div class="col-12 mt-5">
                                                @if ($affiliate->verified_at)
                                                    <!-- Affiliate is verified -->
                                                    @if ($affiliate->status)
                                                        <a class="btn btn-danger"
                                                            onclick="return confirm('Are you sure?');"
                                                            href="{{ route('admin.affiliates.toggle-status', ['affiliate' => $affiliate->reference]) }}">
                                                            Click to Deactivate Agent
                                                        </a>
                                                    @else
                                                        <a class="btn btn-info"
                                                            onclick="return confirm('Are you sure?');"
                                                            href="{{ route('admin.affiliates.toggle-status', ['affiliate' => $affiliate->reference]) }}">
                                                            Click to Activate Agent
                                                        </a>
                                                    @endif
                                                        <a class="btn btn-primary"
                                                            href="{{ route('admin.communications.conversations.show', ['entityCode' => '004', 'entityId' => $affiliate->id])}}">
                                                            <i class="icon-envelope-open"></i>
                                                            Have a conversation
                                                        </a>
                                                    
                                                @elseif ($affiliate->verification_applied)
                                                    <!-- Affiliate has paid-->
                                                    <button class="btn btn-primary" data-toggle="modal" data-target="#verificationModal">
                                                        Click to Verify Agent
                                                    </button>
                                                @endif
                                                
                                                @php($meeting = $affiliate->meeting)
                                                
                                                @if ($meeting && !$affiliate->verified_at)
                                                    <br/>
                                                    <hr/>
                                                    <!-- Affiliate has been scheduled  for a meeting -->
                                                    <h4>Scheduled for a meeting</h4>
                                                    <p>
                                                        <a href="{{ route('admin.meetings.show', ['meeting' => $meeting->id]) }}"
                                                            class="badge badge-info">
                                                            <i class="icon-location-pin"></i>&nbsp;
                                                            {{ $meeting->venue }}
                                                        </a>
                                                        &nbsp;&nbsp;
                                                        <span class="badge badge-primary">
                                                            <i class="icon-clock"></i>&nbsp;
                                                            {{ $meeting->when->format('Y-m-d h:i A') }}    
                                                        </span>
                                                        &nbsp;&nbsp;
                                                        <button class="badge badge-primary" data-toggle="modal" data-target="#meetingModal">
                                                            <i class="fa fa-calendar"></i>
                                                            Reschedule
                                                        </button>
                                                    </p>
                                                @else
                                                    <!-- No meeting yet-->
                                                    <button class="btn btn-primary" data-toggle="modal" data-target="#meetingModal">
                                                        <i class="fa fa-calendar"></i>
                                                        Click to Schedule a meeting
                                                    </button>
                                                @endif

                                                <!-- No meeting yet-->
                                                <button class="btn btn-warning" data-toggle="modal" data-target="#configureSettings">
                                                    <i class="fa fa-settings"></i>
                                                    Configure Settings
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="row ">
                                            <div class="col-12 mt-5">
                                                <h5>Basic Information</h5>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-borderless w-100 mt-4 ">
                                                <tr>
                                                    <td>
                                                        <strong>Full Name :</strong> {{ $affiliate->name }}
                                                    </td>
                                                    <td>
                                                        <strong>Phone :</strong> {{ $affiliate->phone }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Email :</strong> {{ $affiliate->email }}
                                                    </td>
                                                    <td>
                                                        <strong>Location :</strong> {{ $affiliate->address }}                                                    
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>State :</strong> {{ $affiliate->state }}
                                                    </td>
                                                    <td>
                                                        <strong>
                                                            Commission Rates 
                                                            @if($affiliate->verified_at)
                                                                <button class="badge badge-primary" data-toggle="modal" data-target="#updateModal">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                            @endif
                                                        </strong> 
                                                        &nbsp;&nbsp;&nbsp;
                                                        <span class="badge badge-primary">
                                                            Borrowers:
                                                            {{ $affiliate->commission_rate ? $affiliate->commission_rate . '%' : 'Not Set' }}
                                                        </span>
                                                        <span class="badge badge-danger">
                                                            Investors:
                                                            {{ $affiliate->commission_rate_investor ? $affiliate->commission_rate_investor . '%' : 'Not Set' }}
                                                        </span>
                                                        
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>
                                                        <strong>CV :</strong>
                                                        @if (!$affiliate->getOriginal('cv'))
                                                            <button class="btn btn-xs btn-danger" disabled>N/A</button>
                                                        @else
                                                            <a class="btn btn-xs btn-primary" target="_blank" href="{{ $affiliate->cv }}">
                                                                View CV
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                
                                        <div class="row ">
                                            <div class="col-12 mt-5">
                                                <h5 class="mb-5 ">Other Information</h5>
                                                @php($supervisor = $affiliate->supervisor)
                                                <p>
                                                    Supervisor &nbsp;  
                                                    @if ($supervisor)
                                                        <a href="{{ route('admin.affiliates.show', ['reference' => $supervisor->reference ]) }}"
                                                            class="badge badge-info">
                                                            {{ $supervisor->name }}
                                                        </a>
                                                    @else
                                                        <span class="badge badge-dark">No Supervisor</span>
                                                    @endif
                                                    &nbsp;
                                                    <button class="badge badge-primary" data-toggle="modal" data-target="#supervisorModal">
                                                        <i class="fa fa-user-circle-o"></i>
                                                    </button>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <!--<div class="tab-pane fade" id="user-profile-activity" role="tabpanel"-->
                                    <!--    aria-labelledby="user-profile-activity-tab ">-->
                                    <!--    <div class="row ">-->
                                    <!--        <div class="col-12 mt-5 mb-3">-->
                                    <!--            <h5>Basic Information</h5>-->
                                    <!--            <p>Update Affiliate Data</p>-->
                                    <!--        </div>-->
                                            
                                    <!--        <div class="col-12">-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    @if(!$affiliate->verified_at)
    <!-- Verification Modal -->
    
    <div class="modal fade" id="verificationModal" tabindex="-1" role="dialog" aria-labelledby="verificationModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Verify {{ $affiliate->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="verificationForm"
                        action="{{ route('admin.affiliates.verify', ['affiliate' => $affiliate->reference]) }}">
                        @csrf
                        <div class="form-group">
                            <label>Set Commission Rate (Borrowers)</label>
                            <input type="text" name="commission_rate" class="form-control" required/>
                        </div>
                        
                        <div class="form-group">
                            <label>Set Commission Rate (Investors)</label>
                            <input type="text" name="commission_rate_investor" class="form-control" required/>
                        </div>
                        
                        <div class="form-group">
                            <label>Assign Supervisor</label>
                            <select name="supervisor_id" class="form-control" required>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="verificationForm" class="btn btn-success">Verify</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="meetingModal" tabindex="-1" role="dialog" aria-labelledby="meetingModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Schedule a meeting with {{ $affiliate->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="meetingForm"
                        action="{{ route('admin.affiliates.meeting', ['affiliate' => $affiliate->reference]) }}">
                        @csrf
                        <div class="form-group">
                            <label>Select Meeting</label>
                            <select name="meeting_id" class="form-control" required>
                                @foreach($meetings as $meeting)
                                    <option value="{{ $meeting->id }}">{{ $meeting->venue }} ({{ $meeting->when->format('Y-m-d h:i A')}})</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="meetingForm" class="btn btn-success">Schedule</button>
                </div>
            </div>
        </div>
    </div>
    
    @else
    
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Update {{ $affiliate->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="updateForm"
                        action="{{ route('admin.affiliates.update', ['affiliate' => $affiliate->reference]) }}">
                        @csrf
                        <div class="form-group">
        
                            <label>Update Commission Rate (Borrowers)</label>
                            <input type="text" name="commission_rate" value="{{ $affiliate->commission_rate }}" class="form-control" required/>
                        </div>
                        <div class="form-group">
                            <label>Update Commission Rate (Investors)</label>
                            <input type="text" name="commission_rate_investor" value="{{ $affiliate->commission_rate_investor }}" class="form-control" required/>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="updateForm" class="btn btn-success">Update</button>
                </div>
            </div>
        </div>
    </div>
    
    @endif
    
     <div class="modal fade" id="supervisorModal" tabindex="-1" role="dialog" aria-labelledby="supervisorModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Assign supervisor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="supervisorForm"
                        action="{{ route('admin.affiliates.supervisor', ['affiliate' => $affiliate->reference]) }}">
                        @csrf
                        
                        <div class="form-group">
                            <label>Assign Supervisor</label>
                            <select name="supervisor_id" class="form-control" required>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="supervisorForm" class="btn btn-success">Assign</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="configureSettings" tabindex="-1" role="dialog" aria-labelledby="configureSettings" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel-2">Setup {{ $affiliate->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="affiliateSettings"
                        action="{{ route('admin.affiliate.settings', ['affiliate' => $affiliate->reference]) }}">
                        @csrf
                        <div class="form-group">
                            <label>Loan Visibility</label>
                            <select name="loan_vissibility" class="form-control" required>
                                <option value="null">Choose What Affiliate Sees</option>
                                <option value="view_all_loans" {{$affiliate->settings('loan_vissibility') == 'view_all_loans' ? 'selected' : ''}}>View All Loans</option>
                                <option value="view_only_created" {{$affiliate->settings('loan_vissibility') == 'view_only_created' ? 'selected' : ''}}>View Only Created Loans</option>
                            </select>
                        </div>

                        <!-- <div class="form-group"> 
                            <label>Commission Method</label>
                            <select name="commission_method" class="form-control" required>
                                <option value="null">Choose Affiliate Commission Method</option>
                                <option value="disbursement" //{{$affiliate->settings('commission_method') == 'disbursement' ? 'selected' : ''}}>Based On Disbursement</option>
                                <option value="repayment" //{{$affiliate->settings('commission_method') == 'repayment' ? 'selected' : ''}}>Based On Repayment</option>
                            </select>
                        </div>-->

                        <div class="form-group">
                            <label>Loan Booking Status</label>
                            <select name="booking_status" class="form-control" required>
                                <option value="null">Choose Loan Booking Status</option>
                                <option value="refer_only" {{$affiliate->settings('booking_status') == 'refer_only' ? 'selected' : ''}}>Refer Only</option>
                                <option value="book_loans" {{$affiliate->settings('booking_status') == 'book_loans' ? 'selected' : ''}}>Book Loans</option>
                            </select>
                        </div>
                    
        
                        <br><br>
                        <h4>Map Affiliate to Employers</h4>
                        <br>  
                        <p><b>PRIMARY EMPLOYERS</b></p>       
                        @foreach($employers as $employer)
                            <div class="form-group row">
                                <label class="col-6">{{$employer->name}}</label>
                                <div class="col-6">                                 
                                    <input type="checkbox" name="employer_id[]" value="{{$employer->id}}" class="form-control" 
                                            {{ ($mapped->contains($employer->id) ? 'checked' : '') }}>
                                
                                    
                                </div>
                            </div>
                        @endforeach
                        <p><b>MERCHANT EMPLOYERS</b></p> 
				        @foreach($merchantEmployers as $employer)
                            <div class="form-group row">
                                <label class="col-6">{{$employer->name}}</label>
                                <div class="col-6">                                 
                                    <input type="checkbox" name="employer_id[]" value="{{$employer->id}}" class="form-control" 
                                            {{ ($mapped->contains($employer->id) ? 'checked' : '') }}>
                                
                                    
                                </div>
                            </div>
                        @endforeach
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="affiliateSettings" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection