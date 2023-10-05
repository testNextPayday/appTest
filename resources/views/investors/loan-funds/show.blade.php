@extends('layouts.investor')

@section('page-css')
    <style>
        .checked {
            color: orange;
        }
    </style>
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Given Fund - {{ $fund->reference }}</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="fa fa-credit-card text-facebook icon-md"></i>
                        <div class="ml-3">
                          <h6 class="text-facebook">₦ {{ number_format($fund->amount, 2) }}</h6>
                          <p class="mt-2 text-muted card-text">Amount Given</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="fa fa-calendar text-linkedin icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-linkedin">{{ $request->duration }} {{str_plural('month', $request->duration)}}</h6>
                            <p class="mt-2 text-muted card-text">Fund Duration</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="fa fa-money text-google icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-google">₦ {{ number_format($fund->newEmi(), 2) }}</h6>
                            <p class="mt-2 text-muted card-text">Monthly Returns</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6 stretch-card">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div class="align-items-center mb-2">
                        <h4 class="card-title mb-3">Manage Fund</h4>
                        
                        <sell-investment
                            :fund="{{$fund}}"
                            :current-value="{{ $fund->currentValue }}"
                            :repayment="{{$repayment}}"
                            ></sell-investment>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 stretch-card">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div class="align-items-center mb-2">
                        <h4 class="card-title mb-3">Borrower Information</h4>
                        
                        <p><strong>ID:</strong> &nbsp; {{$request->user->reference}}</p>
                        <p><strong>Employer:</strong> &nbsp;</p>
                        <p><strong>Employer State:</strong> &nbsp;</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Bids</h4>
                    <p class="card-description">
                        Bids placed on this asset </p>
                    <div class="table-responsive">
                        <bid-manager :fund="{{$fund}}"
                            :bids="{{$fund->bids()->with('investor')->latest()->get()}}"></bid-manager>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Repayments</h4>
                    <p class="card-description">Loan repayment</p>
                </div>
                <div class="table-responsive">
                    <loanfund-repayment :fundrepayment="{{$fundRepayment}}"></loanfund-repayment>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
