@extends('layouts.auth_new')

@section('title')
No Access
@endsection

@section('content')

<div class="auto-form-wrapper d-flex align-items-center justify-content-center flex-column">
  <!--<div class="nav-get-started">-->
  <!--  <p>Already have an account?</p>-->
  <!--  <a class="btn get-started-btn" href="#">SIGN IN</a>-->
  <!--</div>-->
  
    @if($condition == 'unverified')
      <!-- Check if user has paid verification fee and act accordingly -->
      @php($meeting = $user->meeting)
      
        @if ($meeting)
        <div class="alert alert-info">
            <h4>You've been invited for a meeting!</h4>
            <hr/>
            <h4>VENUE: {{ $meeting->venue }}, {{ $meeting->state }} State</h3>
            <span class="badge badge-dark">
              {{ $meeting->when->format('Y-m-d h:i A') }} 
            </span>
            
            
            @if (!$user->verification_applied)
              <br/><br/>
              <hr/>
              <?php
                $verificationFee = App\Models\Settings::affiliateVerificationFee();
              ?>
        
              <affiliate-verification 
                :verification-fee="{{ $verificationFee }}"
                :paid-verification-fee="{{ $user->verification_applied ? 'true': 'false' }}"
                :verification-disabled="{{ $verificationFee == 0 ? 'true': 'false' }}"
                :email="'{{$user->email}}'"
                :pay-key="'{{config('paystack.publicKey')}}'"></affiliate-verification>  
            @endif
        </div>
      @else
      
        <div class="alert alert-info">
            <h4>Thank you for applying!</h4>
            <hr/>
            Please exercise some patience. You'll get access to your dashboard as soon
            as the ADMIN is done reviewing your application
        </div>
        
      @endif
            
    
    @else
    <div class="alert alert-danger">
      <h4>Deactivated Account</h4>
      <hr/>
      Your account has been deactivated
    </div>
    
    @endif
    
    <init :user="{{ $user }}"></init>
</div>
@endsection