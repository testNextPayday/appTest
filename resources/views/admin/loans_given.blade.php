@extends('layouts.admin')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Fundings for loans</li>
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
                            Loan Funds
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Interest %</th>
                                        <th>Loan Request</th>
                                        <th>Loan Status</th>
                                        <th>Manage</th>
                                        <th>Fund Date</th>
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
                                            <div>{{$loan->loanRequest->reference}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                               ₦ {{$loan->amount}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted text-center">
                                                {{$loan->loanRequest->interest_percentage}} %
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{route('admin.loan-requests.view', ['reference' => $loan->loanRequest->reference])}}">View Request</a>
                                        </td>
                                        <td>
                                            @if($loan->status == 1)
                                            <span class="label label-warning">Pending</span>
                                            @elseif($loan->status == 2)
                                            <span class="label label-info">Active</span>
                                            @elseif($loan->status == 3)
                                            <span class="label label-primary">Cancelled</span>
                                            @else
                                            <span class="label label-danger">Due</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($loan->status == 1)
                                            <div>
                                                <a class="btn btn-xs btn-warning">
                                                    <i class="icon-energy"></i> Pending
                                                </a>
                                            </div>
                                            @elseif($loan->status == 2)
                                            <div>
                                                <a class="btn btn-xs btn-success" href="{{route('admin.loans.given.view', ['id' => encrypt($loan->id)])}}">
                                                    <i class="icon-energy"></i> View
                                                </a>
                                            </div>
                                            @elseif($loan->status == 3)
                                            <div>
                                                <a class="btn btn-xs btn-default">
                                                    <i class="icon-close"></i> Cancelled
                                                </a>
                                            </div>
                                            @elseif($loan->status == 4)
                                            <div>
                                                <a class="btn btn-xs btn-info" href="{{route('admin.loans.given.view', ['id' => encrypt($loan->id)])}}">
                                                    <i class="icon-energy"></i> On Transfer
                                                </a>
                                            </div>
                                            @elseif($loan->status == 5)
                                            <div>
                                                <a class="btn btn-xs btn-warning"
                                                    href="{{route('admin.loans.given.view', ['id' => encrypt($loan->id)])}}">
                                                    <i class="icon-action-redo"></i> Transferred
                                                </a>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                {{ $loan->created_at->format('Y-m-d')}}
                                            </div>
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
                                            You have not given out any loans yet
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