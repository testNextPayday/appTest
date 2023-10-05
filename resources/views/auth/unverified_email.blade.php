@extends('layouts.auth')

@section('title')
Email Verification
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
              
                <h3 class="text-muted strong text-center">Unverified Email</h3>
                
                <p class="text-muted text-center">
                    An email was sent to <a href="#"><b>{{$user->email}}</b></a>, please confirm it to continue.
                </p>
                
                <hr/>
                <div class="text-center">
                    <p>Didn't see the email ?</p>
                    <a href="{{route('email.verification.resend')}}" class="btn btn-primary px-4">Resend Email</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection