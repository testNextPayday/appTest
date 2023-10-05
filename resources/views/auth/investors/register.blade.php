@extends('layouts.auth')

@section('title')
Staff Register
@endsection

@section('content')
<div class="col-md-6">
        <div class="card mx-4">
          <div class="card-body p-4">
              <div class="col-xs-12 col-sm-12 text-center">
                <img class="img-responsive" style="width:15em;" src="{{asset('logo_pack/logo/colored/64.png')}}"/>
              </div>
              <br/>
              <h3 class="text-muted strong text-center">STAFF REGISTER</h3>
              <p class="text-muted text-center">Create your account</p>
            
            @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
            <form class="form-horizontal" method="POST" action="{{ route('staff.register') }}">
            {{ csrf_field() }}
            
            <div class="input-group mb-3">
              <span class="input-group-addon"><i class="icon-user"></i></span>
              <input type="text" name="firstname" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" required value="{{ old('firstname') }}" placeholder="Firstname">
              
            </div>
            
            <div class="input-group mb-3">
              <span class="input-group-addon"><i class="icon-user"></i></span>
              <input type="text" name="lastname" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" required value="{{ old('lastname') }}" placeholder="Lastname">
            </div>
            
            <div class="input-group mb-3">
              <span class="input-group-addon"><i class="icon-user"></i></span>
              <input type="text" name="midname" class="form-control{{ $errors->has('midname') ? ' is-invalid' : '' }}" required value="{{ old('midname') }}" placeholder="Middlename">
            </div>
            
            <div class="input-group mb-3">
              <span class="input-group-addon"><i class="icon-phone"></i></span>
              <input type="number" name="phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" required value="{{ old('phone') }}" placeholder="Phone">
            </div>

            <div class="input-group mb-3">
              <span class="input-group-addon">@</span>
              <input type="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required value="{{ old('email') }}" placeholder="Email">
            </div>

            <div class="input-group mb-3">
              <span class="input-group-addon"><i class="icon-lock"></i></span>
              <input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required placeholder="Password">
            </div>

            <div class="input-group mb-4">
              <span class="input-group-addon"><i class="icon-lock"></i></span>
              <input type="password" name="password_confirmation" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" required placeholder="Repeat password">
            </div>

            <button type="submit" class="btn btn-block btn-success">Create Account</button>
            </form>
          </div>
          
        </div>
      </div>
@endsection