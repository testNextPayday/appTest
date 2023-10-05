@extends('layouts.affiliates')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                Borrower Account {{$user->reference}}
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
                            <li class="nav-item ">
                                <a class="nav-link " id="user-employments-tab" data-toggle="pill" href="#user-employments"
                                    role="tab " aria-controls="user-employments" aria-selected="false ">Employments</a>
                            </li>
                        </ul>
                        
                        <div class="row ">
                            <div class="col-12 col-md-9">
                                <div class="tab-content tab-body" id="profile-log-switch ">
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
                                                                  {{ $employment->position }}&nbsp;
                                                                  <employment-update :employment="{{$employment}}"
                                                                    :csrf-token="'{{csrf_token()}}'"
                                                                    :url="'{{route('affiliates.employments.update', ['employment' => $employment->id])}}'"></employment-update>
                                                              </h3>
                                                            </div>
                                                            <br/>
                                                            <p><strong>Net Salary:</strong> ₦ {{number_format($employment->net_pay, 0)}}</p>
                                                            <p><strong>Date Employed:</strong> {{$employment->date_employed}}</p>
                                                            <p><strong>Date Confirmed:</strong> {{$employment->date_confirmed}}</p>
                                                            <p><strong>MDA:</strong> {{$employment->mda}}</p>
                                                            <p><strong>PayRoll ID:</strong> {{$employment->payroll_id}}  <span style="font-size:20px;cursor:pointer" onclick="document.getElementById('payroll_form').classList.toggle('display-none')"> <i class="fa fa-edit"></i> </span> </p> 
                                                            <div id="payroll_form" class="display-none">
                                                                <form action="{{route('affiliate.employments.update_payroll',['employment'=>$employment->id])}}" method="POST">
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
@endsection