@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">My Profile</h4>
        </div>
    </div>
    <div class="row profile-page">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="profile-header text-white d-none d-md-block">
                        <div class="d-flex justify-content-around">
                            <div class="profile-info d-flex justify-content-center align-items-md-center">
                                <img class="rounded-circle img-lg" src="{{ $investor->avatar }}" alt="profile image">
                                <div class="wrapper pl-4">
                                    <p class="profile-user-name">{{ $investor->name}}</p>
                                    <div class="wrapper d-flex align-items-center">
                                        <p class="profile-user-designation">Nextpayday Investor</p>
                                    </div>
                                </div>
                            </div>
                            <div class="details">
                                <div class="detail-col">
                                    <p>Funded</p>
                                    <p>{{ $investor->loanFunds()->count()}} </p>
                                </div>
                                <div class="detail-col ">
                                    <p>Bids</p>
                                    <p>{{ $investor->bids()->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                  
                    <div class="profile-body pt-0 pt-sm-4">
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
                        <ul class="nav tab-switch " role="tablist ">
                            <li class="nav-item ">
                                <a class="nav-link active " id="user-profile-info-tab" data-toggle="pill" href="#user-profile-info"
                                    role="tab " aria-controls="user-profile-info" aria-selected="true ">Profile</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link " id="user-profile-activity-tab" data-toggle="pill" href="#user-profile-activity"
                                    role="tab " aria-controls="user-profile-activity" aria-selected="false ">Edit Profile</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link " id="user-bank-info-tab" data-toggle="pill" href="#user-bank-info"
                                    role="tab " aria-controls="user-bank-info" aria-selected="false ">Update Bank Data</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link " id="user-security-tab" data-toggle="pill" href="#user-security"
                                    role="tab " aria-controls="user-security" aria-selected="false ">Accounts and Security</a>
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
                                                        <strong>Full Name :</strong> {{ $investor->name }}
                                                    </td>
                                                    <td>
                                                        <strong>Phone :</strong> {{ $investor->phone }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>Email :</strong> {{ $investor->email }}
                                                    </td>
                                                    <td>
                                                        <strong>Location :</strong> {{ $investor->address }}                                                    
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                
                                        <div class="row ">
                                            <div class="col-12 mt-5">
                                                <h5 class="mb-5 ">Other Information</h5>
                                                <div class="stage-wrapper pl-4">
                                                    <div class="stages border-left pl-5 pb-4">
                                                        <div class="btn btn-icons btn-rounded stage-badge btn-inverse-success">
                                                              <i class="mdi mdi-texture "></i>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-2 justify-content-between">
                                                          <h5 class="mb-0 ">Bank Details</h5>
                                                        </div>
                                                        @if($investor->bank)
                                                        <p>Bank Name: {{ $investor->bank->bank_name }}</p>
                                                        <p>Bank Code: {{ $investor->bank->bank_code }}</p>
                                                        <p>Account Number: {{ $investor->bank->account_number }}</p>
                                                        @else
                                                        Bank details unavailable
                                                        @endif
                                                    </div>
                                                    <div class="stages border-left pl-5 pb-4 ">
                                                        <div class="btn btn-icons btn-rounded stage-badge btn-inverse-danger">
                                                            <i class="mdi mdi-download "></i>
                                                        </div>
                                                        <div class="d-flex align-items-center mb-2 justify-content-between ">
                                                            <h5 class="mb-0 ">Documents</h5>
                                                        </div>
                                                        <p>Manage your Nextpayday Documents</p>
                                                        <div class="file-icon-wrapper">
                                                            @if($investor->getOriginal('licence'))
                                                            <a href="{{$investor->licence}}" target="_blank">
                                                                <div class="btn btn-outline-danger file-icon">
                                                                    <i class="mdi mdi-file-pdf "></i>
                                                                </div>
                                                            </a>
                                                            @endif
                                                            @if($investor->getOriginal('reg_cert'))
                                                            <a href="{{$investor->reg_cert}}" target="_blank">
                                                                <div class="btn btn-outline-primary file-icon">
                                                                    <i class="mdi mdi-file-word"></i>
                                                                </div>
                                                            </a>
                                                            @endif
                                                        </div>
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
                                    <div class="tab-pane fade" id="user-profile-activity" role="tabpanel"
                                        aria-labelledby="user-profile-activity-tab ">
                                        <div class="row ">
                                            <div class="col-12 mt-5 mb-3">
                                                <h5>Basic Information</h5>
                                                <p>Update your basic information</p>
                                            </div>
                                            
                                            <div class="col-12">
                                                <form class="forms-sample" action="{{ route('investors.profile') }}"
                                                    method="post" enctype="multipart/form-data">
                                                    
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control{{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                            id="name" placeholder="Name" name="name" required
                                                            value="{{ old('name') ? old('name') : $investor->name }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Email address</label>
                                                        <input type="email" class="form-control{{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                            id="email" placeholder="Email Address" name="email" readonly
                                                            value="{{ old('email') ? old('email') : $investor->email }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="phone">Phone Number</label>
                                                        <input type="text" class="form-control{{ $errors->has('phone') ? 'is-invalid' : '' }}"
                                                            id="phone" placeholder="Phone Number" name="phone" required
                                                            value="{{ old('phone') ? old('phone') : $investor->phone }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="address">Location</label>
                                                        <input type="text" class="form-control{{ $errors->has('address') ? 'is-invalid' : '' }}"
                                                            id="address" placeholder="Address" name="address" required
                                                            value="{{ old('address') ? old('address') : $investor->address }}">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Display Picture</label>
                                                        <input type="file" name="avatar" id="avatar"
                                                            class="form-control{{ $errors->has('avatar') ? 'is-invalid' : '' }}">
                                                    </div>
    
                                                    <button type="submit" class="btn btn-success mr-2 pull-right">Submit</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="user-bank-info" role="tabpanel"
                                        aria-labelledby="user-bank-info-tab ">
                                        <div class="row ">
                                            <div class="col-12 mt-5 mb-3">
                                                <h5>Bank Information</h5>
                                                <p>Update your bank data</p>
                                            </div>
                                            
                                            <div class="col-12">
                                                <form class="forms-sample" action="{{ route('investors.profile.bank') }}"
                                                    method="post">
                                                    
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <label for="bank_code">Bank Name</label>
                                                        <select class="form-control" name="bank_code" required id="bank_code">
                                                            @foreach(config('remita.banks') as $code => $bank)
                                                                @php
                                                                $selected = false;
                                                                if (old('bank_code')) {
                                                                    $selected = old('bank_code') == $code;
                                                                } else if ($investor->bank) {
                                                                    $selected = $investor->bank->bank_code == $code;
                                                                }
                                                                @endphp
                                                                <option value="{{ $code }}" {{ $selected ? 'selected' : ''}}>{{$bank}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="account_number">Account Number</label>
                                                        <input type="number" class="form-control{{ $errors->has('account_number') ? 'is-invalid' : '' }}"
                                                            id="account_number" placeholder="Enter your account number" maxlength="11" name="account_number"
                                                            value="{{ old('account_number') ? old('account_number') : $investor->bank ? $investor->bank->account_number : ''}}">
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-success mr-2 pull-right">Submit</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="user-security" role="tabpanel"
                                        aria-labelledby="user-security-tab ">
                                        
                                        <div class="row ">
                                            <div class="col-12 mt-5 mb-3">
                                                <h5>Dashboard Settings</h5>
                                                <p>Choose between Managed or Self Managed Dashboards</p>
                                            </div>
                                            
                                            <div class="col-12">
                                                <form class="forms-sample" action="{{ route('investors.profile.dashboard') }}" method="post">
                                                    
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <label for="self-managed">
                                                            <input id="self-managed" type="radio" name="is_managed" value="self_managed"
                                                                {{ !$investor->is_managed ? 'checked' : '' }}>
                                                            
                                                            <strong>Self Managed Dashboard</strong>
                                                        </label>
                                                        <hr/>
                                                        <p>Description</p>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label for="managed">
                                                            <input type="radio" id="managed" name="is_managed" value="managed"
                                                                {{ $investor->is_managed ? 'checked' : '' }}>
                                                            
                                                            <strong>Managed Dashboard</strong>
                                                        </label>
                                                        <hr/>
                                                        <p>Description</p>
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-primary mr-2 pull-right">Save Option</button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="row ">
                                            <div class="col-12 mt-5 mb-3">
                                                <h5>Password Change</h5>
                                                <p>Change your account password</p>
                                            </div>
                                            
                                            <div class="col-12">
                                                <form class="forms-sample" action="{{ route('investors.profile.password') }}" method="post">
                                                    
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <label for="current-password">Current Password</label>
                                                        <input type="password" class="form-control{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                                                            id="current-password" placeholder="Current Password" name="current_password" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="new-password">New Password</label>
                                                        <input type="password" class="form-control{{ $errors->has('new_password') ? 'is-invalid' : '' }}"
                                                            id="new-password" placeholder="New Password" name="new_password" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="new-password-confirm">Confirm New Password</label>
                                                        <input type="password" class="form-control{{ $errors->has('new_password_confirmation') ? 'is-invalid' : '' }}"
                                                            id="new-password-confirm" placeholder="Confirm New Password" name="new_password_confirmation" required>
                                                    </div>
                                                    
                                                    <button type="submit" class="btn btn-danger mr-2 pull-right">Change Password</button>
                                                </form>
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