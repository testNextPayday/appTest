@extends('layouts.auth')

@section('content')
              
<div class="col-md-8">
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  
  @if (Auth::user()->is_lender)
    <div class="card">
      <div class="card-header">
          <strong>Lender</strong>
          <small>Section</small>
      </div>
      
      <div class="card-body">
          <a class="btn btn-danger btn-block" href="{{route('lenders.dashboard')}}">
            <i class="icon-arrow-right-circle"></i>
            Go to Lender's Dashboard
          </a>
      </div>
    </div>
  
  @else
  <?php 
    $hasPending = Auth::guard('web')->user()->hasPendinglenderActivationRequest();
  ?>
  <lender-upgrade-form 
      :has-pending="{{$hasPending ? 'true': 'false'}}"
      :verification-fee="{{App\Models\Settings::where('slug', 'lender_activation_fee')->first()->value}}"
      :email="'{{Auth::guard('web')->user()->email}}'"
      :pay-key="'{{config('paystack.publicKey')}}'" />
  @endif
</div>
<div class="col-md-8">
  <div class="card">
    <div class="card-header">
      <div>
        <init :user="{{Auth::guard('web')->user()}}"></init>
        <span><a href="#">{{ config('app.name') }}</a> © {{date('Y')}}</span>
        <span class="pull-right">Powered by <a href="http://olotusquare.co" target="_blank">Olotusquare</a></span>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js')
@endsection