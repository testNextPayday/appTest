@extends('layouts.auth_new')

@section('title')
Reset Password
@endsection

@section('content')
<div class="auto-form-wrapper d-flex align-items-center justify-content-center flex-column">
  <div class="nav-get-started">
    <p>Don't have an account?</p>
    <a class="btn get-started-btn" href="{{ route('affiliates.register') }}">BECOME AN AFFILIATE</a>
  </div>
              
  <form method="POST" action="{{ route('affiliates.passwords.reset') }}">
    {{ csrf_field() }}
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mr-auto">
      <img class="img-responsive" style="width:6em;" src="{{asset('logo_pack/logo/colored/64.png')}}"/>
    </div>
    
    <h3 class="mr-auto">Agent: Password Reset</h3>
    <p class="mb-5 mr-auto">Reset your password now.</p>
    
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
                
    <div class="form-group">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
        </div>
        <input type="email" name="email"
          class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
          value="{{ old('email') }}" placeholder="Email" required />
      </div>
    </div>
                
    <div class="form-group">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
        </div>
        <input type="password" name="password"
          class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
          placeholder="Password" required />
      </div>
    </div>
    
    <div class="form-group">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
        </div>
        <input type="password" name="password_confirmation"
          class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
          placeholder="Password Confirmation" required />
      </div>
    </div>
    <div class="form-group">
      <button class="btn btn-primary submit-btn">RESET PASSWORD</button>
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