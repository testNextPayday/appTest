@extends('layouts.affiliates')

@section('content')


<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Loan Request {{ $loanRequest->reference }}</h4>
            @component('components.lr-status', ['loanRequest' => $loanRequest])
            @endcomponent
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    @component('components.lr-statistics', ['loanRequest' => $loanRequest])
    @endcomponent
    
    <div class="text-right">
        @component('components.lr-action-button', ['loanRequest' => $loanRequest, 'prefix' => 'affiliates'])
        @endcomponent
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

                                        @php($user = $employment->user)
                                        
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
@endsection

@section('page-js')
<script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection