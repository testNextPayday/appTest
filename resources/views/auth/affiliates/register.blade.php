@extends('layouts.auth_new')

@section('title')
Register
@endsection

@section('content')

<div class="auto-form-wrapper d-flex align-items-center justify-content-center flex-column"  style="overflow-y: auto">
  <div class="nav-get-started">
    <p>Already have an account?</p>
    <a class="btn get-started-btn" href="{{ route('affiliates.login') }}">SIGN IN</a>
  </div>
              
  <form method="POST" action="{{ route('affiliates.register') }}" style="width: 100%" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="mr-auto">
      <img class="img-responsive" style="width:6em;" src="{{asset('logo_pack/logo/colored/64.png')}}"/>
    </div>
    
    <h3 class="mr-auto">Agent Registration</h3>
    <p class="mb-5 mr-auto">Enter your details below.</p>
    
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    
    <div class="row">
      <div class="form-group col-sm-6">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
          </div>
          <input type="text" name="name"
            class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
            value="{{ old('name') }}" placeholder="Full Name" required />
        </div>
      </div>
      
      <div class="form-group col-sm-6">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="icon-screen-smartphone"></i></span>
          </div>
          <input type="text" name="phone"
            class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
            value="{{ old('phone') }}" placeholder="Phone Number" required />
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="form-group col-sm-6">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="icon-directions"></i></span>
          </div>
          <input type="text" name="address"
            class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}"
            value="{{ old('address') }}" placeholder="Address" required />
        </div>
      </div>
      
      <div class="form-group col-sm-6">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="icon-compass"></i></span>
          </div>
          <select type="text" name="state" style="height: 100%;"
            class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
            value="{{ old('state') }}">
            @foreach(config('unicredit.states') as $state)
              <option value="{{ $state }}">{{ $state }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    
    <div class="row">            
      <div class="form-group col-sm-12">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
          </div>
          <input type="email" name="email"
            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
            value="{{ old('email') }}" placeholder="Email" required />
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="form-group col-sm-6">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
          </div>
          <input type="password" name="password"
            class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
            placeholder="Password" required />
        </div>
      </div>
      
      <div class="form-group col-sm-6">
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
          </div>
          <input type="password" name="password_confirmation"
            class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
            placeholder="Confirm your password" required />
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="form-group col-sm-12">
        <label>Please attach your CV</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="icon-file"></i></span>
          </div>
          <input type="file" name="cv"
            class="form-control{{ $errors->has('cv') ? ' is-invalid' : '' }}"
            required />
        </div>
      </div>
    </div>
    
    <div class="form-group">
      <button class="btn btn-primary submit-btn">SIGN UP</button>
      <br/><br/>
      <p>Already have an account? <a href="{{route('affiliates.login')}}">Sign in</a></p>
    </div>
                
    <div class="wrapper mt-5 text-gray">
      <p class="footer-text">Copyright © 2018 Nextpayday. All rights reserved.</p>
      <ul class="auth-footer text-gray">
        <li><a href="#">Terms & Conditions</a></li>
        <!--<li><a href="#">Cookie Policy</a></li>-->
      </ul>
    </div>
  </form>
</div>
@endsection