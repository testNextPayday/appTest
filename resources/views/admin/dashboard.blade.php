@extends('layouts.admin-new')

@section('content')
@php
    $admin = auth('admin')->user();
@endphp

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Dashboard</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="fa fa-users text-facebook icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-facebook">{{ App\Models\User::count() }} Borrowers</h6>
                            <p class="mt-2 text-muted card-text text-right">
                                <small>
                                    <a href="{{route('admin.users.index')}}">
                                        VIEW
                                    </a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="fa fa-user text-linkedin icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-linkedin">{{ count($admin->individualInvestors()) }} Ind. Investors</h6>
                            <p class="mt-2 text-muted card-text text-right">
                                <small>
                                    <a href="{{route('admin.investors.individuals')}}">
                                        VIEW
                                    </a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="col-md-3 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="fa fa-briefcase text-twitter icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-twitter">{{ count($admin->corporateInvestors()) }} Corp. Investors</h6>
                            <p class="mt-2 text-muted card-text text-right">
                                <small>
                                    <a href="{{route('admin.investors.corporate')}}">
                                        VIEW
                                    </a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="fa fa-bank text-facebook icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-facebook">{{ count($admin->verifiedEmployers()) }} V. Employers</h6>
                            <p class="mt-2 text-muted card-text text-right">
                                <small>
                                    <a href="{{route('admin.employers.index', ['status' => 'verified'])}}">
                                        VIEW
                                    </a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">{{count($admin->activeLoanRequests())}} Active Loan Requests</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($admin->activeLoanRequests()->sum('amount'), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                  <!--<i class="mdi mdi-clock text-muted"></i>-->
                                  <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                <a class="text-gray" href="{{route('admin.loan-requests.available')}}">
                                    View
                                </a>
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-success px-4 py-2 rounded">
                                <i class="icon-note text-white icon-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
            
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">{{ count($admin->pendingLoanRequests()) }} Pending Loan Requests</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦ {{number_format($admin->pendingLoanRequests()->sum('amount'), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                    <!--<i class="mdi mdi-clock text-muted"></i>-->
                                    <!--<small class=" ml-1 mb-0">Updated: 05:42pm</small>-->
                                </div>
                            </div>
                            <small class="text-gray">
                                <a class="text-gray" href="{{route('admin.loan-requests.pending')}}">
                                    View
                                </a>
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-warning px-4 py-2 rounded">
                                <i class="icon-arrow-left-circle text-white icon-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">{{count($admin->loansOnTransfer())}} Loans on Transfer</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($admin->loansOnTransfer()->sum('amount'), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                  <!--<i class="mdi mdi-clock text-muted"></i>-->
                                  <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small  class=" ml-1 mb-0">Balance:  ₦{{number_format($admin->transferLoansBalance(), 2)}}</small>
                            <br>
                            <small class="text-gray">
                                <a class="text-gray" href="{{route('admin.loans.purchase.available')}}">
                                    View
                                </a>
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-primary px-4 py-2 rounded">
                                <i class="icon-arrow-right-circle text-white icon-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">{{count($admin->activeLoans())}} Active Loans</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($admin->activeLoans()->sum('amount'), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                  <!--<i class="mdi mdi-clock text-muted"></i>-->
                                  <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                  
                                </div>
                            </div>
                            <small  class=" ml-1 mb-0">Balance:  ₦{{number_format($admin->activeLoansBalance(), 2)}}</small>
                            <br>
                            <small class="text-gray">
                                <a class="text-gray" href="{{route('admin.loans.active')}}">
                                    View
                                </a>
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-danger px-4 py-2 rounded">
                                <i class="icon-badge text-white icon-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">{{count($admin->inActiveLoans())}} Inactive Loans</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($admin->inActiveLoansBalance(), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                  <!--<i class="mdi mdi-clock text-muted"></i>-->
                                  <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                  
                                </div>
                            </div>
                            <small  class=" ml-1 mb-0">Balance:  ₦{{number_format($admin->inActiveLoansBalance(), 2)}}</small>
                            <br>
                            <small class="text-gray">
                                <a class="text-gray" href="{{route('admin.loans.inactive')}}">
                                    View
                                </a>
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-primary px-4 py-2 rounded">
                                <i class="icon-badge text-white icon-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">{{count($admin->managedLoans())}} Managed Loans</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($admin->managedLoans()->sum('amount'), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                  <!--<i class="mdi mdi-clock text-muted"></i>-->
                                  <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                 
                                    
                                </div>
                            </div>
                            <small class=" ml-1 mb-0"> Balance : ₦{{number_format($admin->managedLoansBalance(), 2)}}</small>
                            <br>
                            <small class="text-gray">
                                <a class="text-gray" href="{{route('admin.loans.managed')}}">
                                    View
                                </a>
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-danger px-4 py-2 rounded">
                                <i class="icon-badge text-white icon-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">{{count($admin->voidLoans())}} Void Loans</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($admin->voidLoans()->sum('amount'), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                 
                                </div>
                            </div>
                            <small  class=" ml-1 mb-0">Balance:  ₦{{number_format($admin->voidLoansBalance(), 2)}}</small>
                            <br>
                            <small class="text-gray">
                                <a class="text-gray" href="{{route('admin.loans.void')}}">
                                    View
                                </a>
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-danger px-4 py-2 rounded">
                                <i class="icon-badge text-white icon-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">{{count($admin->fulfilledLoans())}} Fulfilled Loans</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-inline-block pt-3">
                            <div class="d-flex">
                                <h2 class="mb-0">₦{{number_format($admin->fulfilledLoans()->sum('amount'), 2)}}</h2>
                                <div class="d-none d-md-flex align-items-center ml-2">
                                  <!--<i class="mdi mdi-clock text-muted"></i>-->
                                  <!--<small class=" ml-1 mb-0">Updated: 9:10am</small>-->
                                </div>
                            </div>
                            <small  class=" ml-1 mb-0">All Balances:  ₦{{number_format($admin->fulfilledLoanBalance(), 2)}}</small>
                            <br>
                            <small class="text-gray">
                                <a class="text-gray" href="{{route('admin.loans.fulfilled')}}">
                                    View
                                </a>
                            </small>
                        </div>
                        <div class="d-inline-block">
                            <div class="bg-danger px-4 py-2 rounded">
                                <i class="icon-badge text-white icon-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    
    <div class="row">
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="icon-wallet text-success icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-success">₦ {{ number_format($admin->investorsWalletBalances(), 2)  }}</h6>
                            <p class="mt-2 text-muted card-text text-right">
                                <small>
                                    <a class="text-success" href="{{ route('admin.accounts', ['model' => 'investors', 'type'=> 'wallet']) }}">
                                        Total Investors Wallet Balance
                                    </a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="icon-diamond text-info icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-info">₦ {{ number_format($admin->investorsPortFolioSize(), 2) }}</h6>
                            <p class="mt-2 text-muted card-text text-right">
                                <small>
                                    <a href="{{ route('admin.accounts', ['model' => 'investors', 'type'=> 'escrow']) }}" class="text-info">
                                        Total Investors Portfolio Balance
                                    </a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="icon-credit-card text-primary icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-primary">₦ {{ number_format($admin->borrowersWalletBalances(), 2)  }}</h6>
                            <p class="mt-2 text-muted card-text text-right">
                                <small>
                                    <a class="text-primary" href="{{ route('admin.accounts', ['model' => 'borrowers', 'type'=> 'wallet']) }}">
                                        Total Borrowers Wallet Balance
                                    </a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        <i class="icon-star text-danger icon-md"></i>
                        <div class="ml-3">
                            <h6 class="text-danger">₦ {{ number_format($admin->borrowersEscrowBalances(), 2)  }}</h6>
                            <p class="mt-2 text-muted card-text text-right">
                                <small>
                                    <a href="{{ route('admin.accounts', ['model' => 'borrowers', 'type'=> 'escrow']) }}" class="text-danger">
                                        Total Borrowers Escrow Balance
                                    </a>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row align-items-top">
                        
                        <div class="ml-3">
                            <h4 class="mb-2">Download Borrowers/Investors</h4>
                            <form action="{{ Route('admin.downloadData') }}" method="POST" class=mt-3>@csrf
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="table">Category</label>
                                        <select name="table" id="" class="form-control">
                                            <option value="borrowers">Borrowers</option>
                                            <option value="investors">Investors</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="table">Date</label>
                                        <input type="date" class="form-control" name="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="table">.</label>
                                        <button class="btn btn-sm btn-primary">Download excel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</div>
@endsection

@section('page-js')
<script src="{{asset('coreui/js/views/main.js')}}"></script>
@endsection