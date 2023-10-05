@extends('layouts.auth_new')

@section('title')
Login
@endsection

@section('content')

<div class="auto-form-wrapper d-flex align-items-center justify-content-center flex-column">
    <!--<div class="nav-get-started">-->
    <!--    <p>Don't have an account?</p>-->
    <!--    <a class="btn get-started-btn" href="#">GET STARTED</a>-->
    <!--</div>-->
          
    <div class="container" style="width:100%">    
        <div class="mr-auto">
            <img class="img-responsive" style="width:6em;" src="{{asset('logo_pack/logo/colored/64.png')}}"/>
        </div>
        
        <h3 class="mr-auto">Become an Investor</h3>
        <p class="mb-5 mr-auto">Apply for Investor verification and upgrade.</p>
        
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
                    
        @php 
            $investor = Auth::guard('investor')->user();
            $hasPending = $investor->hasPendingVerification();
        @endphp
        
        <investor-registration 
            :has-pending="{{$hasPending ? 'true': 'false'}}"
            :verification-fee="{{App\Models\Settings::investorVerificationFee()}}"
            :email="'{{$investor->email}}'"
            :pay-key="'{{config('paystack.publicKey')}}'"
            :is-company="{{ $investor->is_company }}"></investor-registration>
    </div>
    
    <div class="wrapper mt-5 text-gray">
        <init :user="{{$investor}}"></init>
        <p class="footer-text">Copyright © 2018 Nextpayday. All rights reserved.</p>
        <ul class="auth-footer text-gray">
            <li><a href="#">Terms & Conditions</a></li>
            <!--<li><a href="#">Cookie Policy</a></li>-->
        </ul>
    </div>
</div>
@endsection