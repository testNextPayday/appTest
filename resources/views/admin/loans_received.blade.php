@extends('layouts.admin')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Loans Collected</li>
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
                            Loans Received
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Interest %</th>
                                        <th class="text-center">Tenure</th>
                                        <th>Loan Request</th>
                                        <th>Loan Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($loans))
                                    @foreach($loans as $loan)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$loan->reference}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                               ₦ {{$loan->amount}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$loan->interest_percentage}}
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                {{$loan->due_date->diffInMonths($loan->created_at)}} Months ({{$loan->due_date->diffForHumans()}})
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{route('admin.loan-requests.view', ['reference' => $loan->loanRequest->reference])}}">View Request</a>
                                        </td>
                                        <td>
                                            @if($loan->status == 1)
                                            <button class="btn btn-primary">Active</button>
                                            @elseif($loan->status == 2)
                                            <button class="btn btn-success">Fulfilled</button>
                                            @else
                                            <button class="btn btn-danger">Defaulting</button>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('admin.loans.view', ['reference' => $loan->reference])}}" class="btn btn-primary">
                                                <i class="icon-eye"></i>
                                                View Loan
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="8" class="text-right">
                                            {{$loans->links()}}
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            There are no received loans
                                        </td>
                                    </tr>
                                    @endif
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