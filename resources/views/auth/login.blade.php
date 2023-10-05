@extends('layouts.auth')

@section('title')
Login
@endsection

@section('page-css')
<style>
  @media screen and (min-width: 1000px) {
    .investor-login-section {
      display:none; 
    }
  }
</style>

@endsection

@section('content')
 <div class="col-md-8">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <div class="col-xs-12 col-sm-12 text-center">
                <img class="img-responsive" style="width:15em;" src="{{asset('logo_pack/logo/colored/128.png')}}"/>
              </div>
              <br/>
              <h3 class="text-muted strong text-center">LOGIN</h3>
              <p class="text-muted text-center">Sign In to your account</p>
             
              
              @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
                                
             <form class="form-horizontal" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}
            
            <div class="input-group mb-3">
              <span class="input-group-addon"><i class="fa fa-phone-square"></i></span>
              <input type="text" name="phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" required value="{{ old('phone') }}" placeholder="Phone">
            </div>
             
              <div class="input-group mb-3">
              <span class="input-group-addon"><i class="icon-lock"></i></span>
              <input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required placeholder="Password">
            </div>
              <div class="row">
                <div class="col-6">
                  <button type="submit" class="btn btn-primary px-4">Login</button>
                </div>
                <div class="col-6 text-right">
                  <!-- <a class="btn btn-link px-0" href="{{url('password/reset')}}">Forgot password?</a> -->
                  <a class="btn btn-link px-0" href="{{route('password.request.phone')}}">Forgot password?</a>
                </div>
              </div>
              <br/>
              <div class="row investor-login-section">
                <div class="col-12 text-center">
                  <a href="{{route('investors.login')}}" class="btn btn-block btn-danger mt-3" style="background-color:#a61e23">
                    LOGIN AS AN INVESTOR
                  </a>
                </div>
                <br/><br/><br/>
              </div>
              
              <div class="row">
                <div class="col-12">
                  <p>Dont have an account? <a class="btn btn-link px-0" href="{{route('register')}}">Sign up here</a></p>
                </div>
              </div>
            </div>
          </div>
          <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
            <div class="card-body text-center">
              <div>
                <h2>INVESTOR LOGIN</h2>
                <!--<p>Are you an investor?</p>-->
                <br/><br/><br/><br/>
                <strong>
                  <a href="{{route('investors.login')}}" class="btn btn-danger mt-3" style="background-color:#a61e23">
                    LOGIN AS AN INVESTOR
                  </a>
                </strong>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endsection