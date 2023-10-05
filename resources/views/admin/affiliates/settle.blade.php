@extends('layouts.admin-new')

@section('page-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-10">
            <h4>Settle Affilliates on Past Loans</h4>
           <settle-affiliates :url="'{{route('admin.affiliates.settle.commission')}}'" :token="'{{csrf_token()}}'" :loanrequests="{{$loanRequests}}" :affiliates="{{$affiliates}}"></settle-affiliates>
        </div>
    </div>

    <div class="row mb-4">

        <div class="col-10">
           
            <h4>Settle Affilliates on Past Investor Wallet Funds</h4>
           <settle-affiliates-wallet-fund :url="'{{route('admin.investors.pay-fund-commission')}}'" :token="'{{csrf_token()}}'" ></settle-affiliates-wallet-fund>
        </div>

    </div>

    <div class="row mb-4">

        <div class="col-10">
        
            <h4>Settle Affilliates on Promissory Note Investment</h4>
            <settle-affiliates-promissory-note :url="'{{route('admin.investors.pay-note-commission')}}'" :token="'{{csrf_token()}}'" ></settle-affiliates-promissory-note>
        </div>

    </div>
    
</div>
@endsection

@section('page-js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <script> $(function() {$('select').selectpicker(); }); </script>
@endsection