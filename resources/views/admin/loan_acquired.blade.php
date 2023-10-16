@extends('layouts.admin')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.loans.acquired')}}">Acquired Loans</a></li>
        <li class="breadcrumb-item active">{{$loan->reference}}</li>
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
                            Loan Details
                        </div>
                        <div class="card-body">
                            <div class="card-group mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-diamond"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($loan->amount, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Acquisition Amount</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-diamond"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($currentValue, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Current Value</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-note"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loaneeLoan->due_date->diffInMonths($loaneeLoan->created_at)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Loan Tenure (Months)</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-compass"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loan->created_at}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Acquisition Date</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-speedometer"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loaneeLoan->due_date}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Due Date</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            @if($loan->status == 1)
                            <button class="btn btn-warning">Pending</button>
                            @elseif($loan->status == 2)
                            <button class="btn btn-success">Active</button>
                            @elseif($loan->status == 3)
                            <button class="btn btn-default">Cancelled</button>
                            @elseif($loan->status == 4)
                            <button class="btn btn-info">Up For Transfer (₦ {{$loan->sale_amount}})</button>
                            @elseif($loan->status == 5)
                            <button class="btn btn-success">Transferred</button>
                            @else
                            <button class="btn btn-success">Fulfilled</button>
                            @endif
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            @if($loan->status == 4 || $loan->status == 5)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Bids for this loan
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-people"></i></th>
                                        <th>Bidder</th>
                                        <th class="text-center">Offer</th>
                                        <th>Bid Date</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loan->bids()->latest()->get() as $bid)
                                    <tr>
                                        <td class="text-center">
                                            <div class="avatar">
                                                <img src="{{$bid->user->avatar}}" class="img-avatar" alt="avatar">
                                                <span class="avatar-status badge-success"></span>
                                             </div>
                                        </td>
                                        <td>
                                            <div>{{$bid->user->lastname}} {{$bid->user->firstname}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                                ₦ {{$bid->amount}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$bid->created_at}}
                                            </div>
                                        </td>
                                        <td>
                                            
                                            @if($bid->status == 1)
                                            <a class="btn btn-sm btn-primary">Pending</a>
                                            @elseif($bid->status == 2)
                                            <a class="btn btn-sm btn-success">Accepted</a>
                                            @elseif($bid->status == 3)
                                            <a class="btn btn-sm btn-danger">Rejected</a>
                                            @else
                                            <a class="btn btn-sm btn-default">Cancelled</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            There are no bids for this loan
                                        </td>
                                    
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            @endif
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Repayment Plan
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Interest</th>
                                        <th>Principal</th>
                                        <th>Total Amount</th>
                                        <th>Due Date</th>
                                        <th class="text-center">Principal Balance</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($repaymentPlans as $plan)
                                    @if($plan->payday < $loan->transfer_date)
                                        @continue
                                    @endif
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$plan->interest * $fundFraction}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                                {{$plan->principal * $fundFraction}}  
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted text-center">
                                                {{($plan->interest * $fundFraction) + ($plan->principal * $fundFraction)}}
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="text-muted">
                                                {{$plan->payday}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted text-center">
                                                @if($loop->iteration === $loan->loanRequest->duration)
                                                0
                                                @else
                                                {{$plan->balance * $fundFraction}}
                                                @endif
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                @if($loan->status == 5 && $plan->payday > $loan->transfer_date)
                                                    <a class="btn btn-xs btn-info" href="#">Transferred</a>
                                                @else
                                                    @if($plan->status)
                                                    <a class="btn btn-xs btn-success" href="#">Paid</a>
                                                    @else
                                                    <a class="btn btn-xs btn-warning" href="#">Not Due</a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Repayment Plan has not been set up
                                        </td>
                                    
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection