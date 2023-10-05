@extends('layouts.admin')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Loans</a></li>
        <li class="breadcrumb-item active">Given</li>
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
                            All Loans
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th class="text-center">Return Amount</th>
                                        <th>Interest %</th>
                                        <th class="text-center">Due Date</th>
                                        <th>Loan Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loans as $loan)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$loan->reference}}</div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                               ₦ {{$loan->amount}}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                ₦ {{($loan->amount + ($loan->amount * $loan->interest_percentage / 100))}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$loan->interest_percentage}}
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                {{$loan->due_date}} ({{$loan->due_date->diffForHumans()}})
                                            </div>
                                        </td>
                                        <td>
                                            @if($loan->status == 1)
                                            <span class="label label-warning">Active</span>
                                            @elseif($loan->status == 2)
                                            <span class="label label-info">Up For Transfer</span>
                                            @elseif($loan->status == 3)
                                            <span class="label label-primary">Transferred</span>
                                            @elseif($loan->status == 4)
                                            <span class="label label-success">Fulfilled</span>
                                            @else
                                            <span class="label label-danger">Due <small>We are on it</small></span>
                                            @endif
                                        </td>
                                        
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            You have not given out any loans yet
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