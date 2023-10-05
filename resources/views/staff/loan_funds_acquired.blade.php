@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Acquired Loans</li>
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
                            Loans Acquired
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Loan Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loanFunds as $loan)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$loan->reference}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                               ₦ {{$loan->amount}}
                                            </div>
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
                                                <a class="btn btn-xs btn-success" href="{{route('staff.funds.acquired.view', ['id' => encrypt($loan->id)])}}">
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
                                                <a class="btn btn-xs btn-info" href="#">
                                                    <i class="icon-energy"></i> On Transfer
                                                </a>
                                            </div>
                                            @elseif($loan->status == 5)
                                            <div>
                                                <a class="btn btn-xs btn-warning"
                                                    href="#">
                                                    <i class="icon-action-redo"></i> Transferred
                                                </a>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            You have not acquired any loans
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