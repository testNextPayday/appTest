@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">My Profile</li>
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
            <div class="row justify-content-center">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{strtoupper(Auth::guard('staff')->user()->reference)}}</strong>
                            <small>Reference</small>
                        </div>
                        
                        <div class="card-body">
                            <form class="form-horizontal" method="POST" action="{{route('staff.profile.update')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="control-label" for="firstname"><strong>Firstname</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-user"></i></span>
                                        <input type="text" name="firstname" id="firstname"
                                            class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" 
                                            required value="{{ old('firstname') ? old('firstname') : Auth::guard('staff')->user()->firstname }}" 
                                            placeholder="Firstname" title="Your First Name">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="lastname"><strong>Lastname</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-people"></i></span>
                                        <input type="text" name="lastname" id="lastname" 
                                            class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" 
                                            required value="{{ old('lastname') ? old('lastname') : Auth::guard('staff')->user()->lastname }}" 
                                            placeholder="Lastname" title="Your Family Name">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="midname"><strong>Middle Name</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-user"></i></span>
                                        <input type="text" name="midname" id="midname"
                                            class="form-control{{ $errors->has('midname') ? ' is-invalid' : '' }}" 
                                            required value="{{ old('midname') ? old('midname') : Auth::guard('staff')->user()->midname }}" 
                                            placeholder="Middlename" title="Your Middle Name">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="phone"><strong>Phone Number</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-phone"></i></span>
                                        <input type="number" name="phone" id="phone"
                                            class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" 
                                            required value="{{ old('phone') ? old('phone') : Auth::guard('staff')->user()->phone }}"
                                            placeholder="Phone Number" title="Your Phone Number">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="email"><strong>Email Address</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon">@</span>
                                        <input type="email" name="email" id="email"
                                            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" 
                                            required value="{{ Auth::guard('staff')->user()->email }}" 
                                            placeholder="Email" readonly>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="avatar"><strong>Profile Picture</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-user"></i></span>
                                        <input type="file" name="avatar" id="avatar"
                                            class="form-control{{ $errors->has('avatar') ? ' is-invalid' : '' }}" 
                                            title="Select a profile image">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="gender"><strong>Gender</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-puzzle"></i></span>
                                        <select class="form-control{{ $errors->has('gender') ? ' is-invalid': ''}}" name="gender" id="gender" required>
                                            <option disabled>Choose your gender</option>
                                            <option value="1" {{(Auth::guard('staff')->user()->gender === 1) ? 'selected': ''}}>Male</option>
                                            <option value="2" {{(Auth::guard('staff')->user()->gender === 2) ? 'selected': ''}}>Female</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="address"><strong>Address</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-compass"></i></span>
                                        <input type="text" name="address" id="address" 
                                            class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" 
                                            required value="{{ old('address') ? old('address') : Auth::guard('staff')->user()->address }}" 
                                            placeholder="Address" title="Your Residential Address">
                                    </div>    
                                </div>
                                
                                
                                <button type="submit" class="btn btn-block btn-success">Update Profile Information</button>
                            </form>          
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <strong>Bank Details</strong>
                            
                        </div>
                        
                        <div class="card-body">

                            <form class="form-horizontal" method="POST" action="{{route('staff.profile.update-bank')}}">

                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="control-label" for="account_number"><strong>Account Number</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-user"></i></span>
                                        <input type="text" name="account_number" id="account_number"
                                            class="form-control{{ $errors->has('account_number') ? ' is-invalid' : '' }}" 
                                            required value="{{ old('account_number') ? old('account_number') : optional(Auth::guard('staff')->user()->banks->last())->account_number }}" 
                                            placeholder="Account Number" title="Your Account Number">
                                    </div>    
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label" for="bank_code"><strong>Select Bank</strong></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-people"></i></span>
                                        <select type="text" name="bank_code" id="bank_code" 
                                            class="form-control{{ $errors->has('bank_code') ? ' is-invalid' : '' }}" 
                                            required >
                                           @foreach(config('remita.banks') as $index=>$value)

                                                @if(optional(Auth::guard('staff')->user()->banks->last())->bank_code == $index)

                                                    <option selected="true" value="{{$index}}">{{$value}}</option>
                                                @else
                                                    <option value="{{$index}}">{{$value}}</option>
                                                @endif
                                           @endforeach
                                           
                                           </select>
                                    </div>    
                                </div>
                            
                                
                                <button type="submit" class="btn btn-block btn-success">Update Bank Details</button>
                            </form>          
                        </div>
                    </div>
                </div>
            </div>
                        
        </div>

    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection