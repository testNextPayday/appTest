@extends('layouts.auth')

@section('title')
Forgot Password
@endsection

@section('content')
 <div class="col-md-8">
    <div class="card-group">
        <div class="card p-4">
        <div class="card-body">
            <div class="col-xs-12 col-sm-12 text-center">
            {{-- <img class="img-responsive" style="width:15em;" src="{{asset('logo_pack/logo/colored/128.png')}}"/> --}}
            </div>

            <password-reset-phone></password-reset-phone>
            
        
        </div>
        </div>
        <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
        <div class="card-body text-center">
            <div>
            <h2>Sign up</h2>
            <p>Don't have an account yet? Sign up to get started!</p>
            <a href="https://nextpayday.ng/register" class="btn btn-primary active mt-3">Sign up here</a>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection