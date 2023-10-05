@extends('layouts.admin-new')

@section('content')
@php($insurance = 2.5/100 * $loanRequest->amount)

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Request {{$loanRequest->reference}}</h4>
            @component('components.lr-status-admin', ['loanRequest' => $loanRequest])
            @endcomponent
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    @component('components.lr-statistics', ['loanRequest' => $loanRequest])
    @endcomponent
    
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">ADMIN ACTION</h4>
                    <br/>
                    <div class="text-center">
                        @component('components.lr-action-button-admin', ['loanRequest' => $loanRequest, 'prefix'=>'admin'])
                        @endcomponent
                    </div>
                    <br/><br/>
                    @if (($loanRequest->status == 2 && $loanRequest->percentage_left == 0) || ($loanRequest->status == 4 && !$loanRequest->loan))
                        @component('components.lr-loan-setup-form', ['loanRequest' => $loanRequest,'url'=>route('admin.loan-requests.prepare-loan', ['loanRequest' => $loanRequest->reference])])
                        @endcomponent
                    @endif
                    
                    @if($loanRequest->status == 5 && $loanRequest->decline_reason)
                    <div class="pt-3">
                        <h4 class="card-title">
                            <strong><em>Reason for Decline:</em></strong> 
                            &nbsp;{{ $loanRequest->decline_reason }}
                        </h4>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <br/>
    
    <div class="row profile-page">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <div style="display: flex; align-items: center;">
                            <img src="{{ $user->avatar }}" width="150" class="img-thumbnail img-circle"/>
                            <div class="px-3">
                                <h4>{{ $user->name }}</h4>
                                <p class="profile-user-designation">{{ strtoupper($user->reference) }}</p>
                                <p class="profile-user-designation">Payroll ID :{{$user->employments->first()->payroll_id}}</p>
                                <div>
                                    @if($user->is_active)
                                    <span class="badge badge-success">
                                        ACTIVE
                                    </span>
                                    @else
                                    <span class="badge badge-danger">
                                        INACTIVE
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-body pt-0 pt-sm-4">
                        <ul class="nav tab-switch " role="tablist ">
                            <li class="nav-item ">
                                <a class="nav-link active " id="request-docs-tab" data-toggle="pill" href="#request-docs"
                                    role="tab " aria-controls="request-docs" aria-selected="true ">Request Docs</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link " id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info"
                                    role="tab " aria-controls="user-profile-info" aria-selected="true ">User Information</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link " id="user-employments-tab" data-toggle="pill" href="#user-employments"
                                    role="tab " aria-controls="user-employments" aria-selected="false ">Employments</a>
                            </li>
                        </ul>
                        
                        <div class="row ">
                            <div class="col-12 col-md-9">
                                <div class="tab-content tab-body" id="profile-log-switch ">
                                    <div class="tab-pane fade show active pr-3 " id="request-docs" role="tabpanel"
                                        aria-labelledby="request-docs-tab">
                                        
                                        <div class="row ">
                                            <div class="col-12 mt-5">
                                                <h5>Submitted Documents</h5>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="row">
                                            <div class="col-12 mt-2">
                                                <p>
                                                    @if($loanRequest->getOriginal('bank_statement'))
                                                    <a href="{{$loanRequest->bank_statement}}" target="_blank"
                                                        class="btn btn-outline-info btn-sm">Bank Statement</a>&nbsp;&nbsp;
                                                    @endif
                                                    
                                                    @if($loanRequest->getOriginal('pay_slip'))
                                                    <a href="{{$loanRequest->pay_slip}}" target="_blank"
                                                        class="btn btn-outline-danger btn-sm">Pay Slip</a>&nbsp;&nbsp;
                                                    
                                                    @endif
                                                    
                                                    
                                                    @php($employment = $loanRequest->employment)
                                                    @if($employment && $employment->getOriginal('employment_letter'))
                                                    <a href="{{$employment->employment_letter}}" target="_blank"
                                                        class="btn btn-outline-success btn-sm">Employment Letter</a>&nbsp;&nbsp;
                                                    @endif
                                                    
                                                    @if($employment && $employment->getOriginal('confirmation_letter'))
                                                    <a href="{{$employment->confirmation_letter}}" target="_blank"
                                                        class="btn btn-outline-danger btn-sm">Confirmation Letter</a>&nbsp;&nbsp;
                                                    @endif
                                                    
                                                    @if($employment && $employment->getOriginal('work_id_card'))
                                                    <a href="{{$employment->work_id_card}}" target="_blank"
                                                        class="btn btn-outline-primary btn-sm">Work ID</a>
                                                    @endif
                                                    
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade pr-3 " id="user-profile-info" role="tabpanel"
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
                                        
                                        <div class="row">
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
                                                        <?php $userBank = $user->bankDetails()->latest()->first(); ?>
                                                        @if ($userBank)
                                                        <p>Bank Name: {{$userBank->bank_name}}</p>
                                                        <p>Account Number: {{$userBank->account_number}}</p>
                                                        <p>Bank Code: {{ $userBank->bank_code }}</p>
                                                        @else
                                                        Bank details unavailable
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="user-employments" role="tabpanel"
                                        aria-labelledby="user-employments-tab ">
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
                                                                    {{ $employment->position }}
                                                                </h3>
                                                            </div>
                                                            <br/>
                                                            <p><strong>Net Salary:</strong> ₦ {{number_format($employment->net_pay, 0)}}</p>
                                                            <p><strong>Date Employed:</strong> {{$employment->date_employed}}</p>
                                                            <p><strong>Date Confirmed:</strong> {{$employment->date_confirmed}}</p>
                                                            <p><strong>Employer:</strong> {{$employment->employer->name}}</p>
                                                            <p><strong>Employer Email:</strong> {{$employment->employer->email}}</p>
                                                            <p><strong>Employer Phone:</strong> {{$employment->employer->phone}}</p>
                                                            <p><strong>Employer Address:</strong> {{$employment->employer->address}}</p>
                                                            <p><strong>Payroll ID : </strong> {{$employment->payroll_id}}</p>
                                                            <p><strong>MDA : </strong> {{$employment->mda}}</p>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($loanRequest->status > 1)
        <br/><br/>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4>Funds for this Request ({{$loanRequest->funds()->count()}})</h4>
                        <table class="table table-responsive-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Investor Reference</th>
                                    <th>Investor Name</th>
                                    <th class="text-center">Offer</th>
                                    <th>Fund Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loanRequest->funds()->latest()->get() as $fund)
                                    <tr>
                                        <td>
                                            <div>
                                                {{$fund->investor->reference}}
                                             </div>
                                        </td>
                                        <td>
                                            <div>{{$fund->investor->name}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                                ₦ {{$fund->amount}} <span>({{$fund->percentage}}%)</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$fund->created_at->toDateString()}}
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
    <br/><br/>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Previous Loan Requests</h4>
                    <br/>
                    
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="order-listing" class="table">
                                <thead>
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
                                    @foreach($loanRequests as $prevLoanRequest)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$prevLoanRequest->reference}}</div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                               ₦ {{$prevLoanRequest->amount}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$prevLoanRequest->interest_percentage}}
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                {{$prevLoanRequest->created_at}}
                                            </div>
                                        </td>
                                        <td>
                                            @component('components.lr-status-admin', ['loanRequest' => $prevLoanRequest])
                                            @endcomponent
                                        </td>
                                        <td>
                                            <a class="badge badge-primary" href="{{route('admin.loan-requests.view', ['reference' => $prevLoanRequest->reference])}}"><i class="icon-eyes"></i> View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            There are no previous loan requests
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/.col-->
    </div>
</div>


<!-- /.modal -->
@endsection

@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection