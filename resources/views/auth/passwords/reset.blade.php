@extends('layouts.auth')

@section('title')
Login
@endsection

@section('content')
 <div class="col-md-8">
        <div class="card-group">
          <div class="card p-4">
            <div class="card-body">
              <div class="col-xs-12 col-sm-12 text-center">
                {{-- <img class="img-responsive" style="width:15em;" src="{{asset('logo_pack/logo/colored/128.png')}}"/> --}}
              </div>
              <br/>
              <h3 class="text-muted strong text-center">Reset Password</h3>
              <p class="text-muted text-center">Enter account details</p>
             
              
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
                                
             <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
            {{ csrf_field() }}
            
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="input-group mb-3">
              <span class="input-group-addon">@</span>
              <input type="text" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required value="{{ $email or old('email') }}" placeholder="Email">
            </div>
             
              <div class="input-group mb-3">
              <span class="input-group-addon"><i class="icon-lock"></i></span>
              <input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required placeholder="Password">
            </div>
            
            <div class="input-group mb-3">
              <span class="input-group-addon"><i class="icon-lock"></i></span>
              <input type="password" name="password_confirmation" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" required placeholder="Confirm Password">
            </div>
            
              <div class="row">
                <div class="col-6">
                  <button type="submit" class="btn btn-primary px-4">Reset Password</button>
                </div>
                
              </div>
            </div>
          </div>
          </form>
          <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
            <div class="card-body text-center">
              <div>
                <h2>Sign up</h2>
                <p>Don't have an account yet? Sign up to get started!</p>
                <a href="{{route('register')}}" class="btn btn-primary active mt-3">Sign up here</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endsection