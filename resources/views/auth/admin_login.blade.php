@extends('layouts.auth')

@section('title')
Login
@endsection

@section('content')
 <div class="col-md-4">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <div class="col-xs-12 col-sm-12 text-center">
                <img class="img-responsive" style="width:15em;" src="{{asset('logo_pack/logo/colored/128.png')}}"/>
              </div>
              <br/>
              <h3 class="text-muted strong text-center">ADMIN LOGIN</h3>
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
                                
             <form class="form-horizontal" method="POST" action="{{ route('admin.login') }}">
            {{ csrf_field() }}
            
            <div class="input-group mb-3">
              <span class="input-group-addon">@</span>
              <input type="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required value="{{ old('email') }}" placeholder="Email">
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
                  <button type="button" class="btn btn-link px-0">Forgot password?</button>
                </div>
              </div>
            </div>
          </div>
          <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%;display:none">
            <div class="card-body text-center">
              <div>
                <h2>Sign up</h2>
                <p></p>
                <button type="button" class="btn btn-primary active mt-3">Register Now!</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endsection