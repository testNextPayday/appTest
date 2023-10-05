@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Investor</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            @if ($errors->any())
                <div class="row justify-content-center">
                    <div class="col-sm-6">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Account Summary
                            <span class="pull-right">
                                @if($investor->is_active)
                                <span class="text-success">Active</span>
                                @else
                                <span class="text-danger">Inactive</span>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="card-group mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-diamond"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$investor->reference}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Reference</small>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-pie-chart"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$investor->loanFunds()->count()}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Loan Fund(s)</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-credit-card"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($investor->wallet, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Wallet Balance</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-lock"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($investor->escrow, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Escrow Balance</small>
                                    </div>
                                </div>
            
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Investor Details
                            <span class="pull-right">
                                @if($investor->is_active)
                                <span class="text-success">Active</span>
                                @else
                                <span class="text-danger">Inactive</span>
                                @endif
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-2">
                                    <img src="{{$investor->avatar}}" style="width:100%; border: 1px solid #000; border-radius: 5%"/>
                                </div>
                                <div class="col-sm-4">
                                    <p>Name: <strong>{{$investor->name}}</strong></p>
                                    <p>Email: <strong>{{$investor->email}}</strong></p>
                                    <p>Phone: <strong>{{$investor->phone}}</strong></p>
                                    <p>Address: <strong>{{$investor->address}}</strong></p>
                                    <p>LGA: <strong>{{$investor->lga}}</strong></p>
                                    <p>City, State: <strong>{{$investor->city}} {{$investor->state}}</strong></p>
                                </div>
                            </div>
                            <br/>
                        </div>
                        <div class="card-footer text-right">
                            
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
        </div>

    </div>
    <!-- /.conainer-fluid -->
    
</main>
@endsection

@section('page-js')
@endsection