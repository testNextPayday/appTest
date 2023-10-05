@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Setup Investment Profile</h4>
        </div>
    </div>
    <div class="row profile-page">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <wallet-vault-transfer :investor="{{$investor}}"></wallet-vault-transfer>
                  
                    
                   
                </div>
            </div>
            <hr>
            <div class="card">
                <div class="card-body">
                    <setup-investment-profile :investor="{{$investor}}" :employers="{{\App\Models\Employer::primary()->get(['name','id'])}}"></setup-investment-profile>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
@endsection