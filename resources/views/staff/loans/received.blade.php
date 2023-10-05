@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Received Loans</li>
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
                            <table id="order-listing" class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>
                                            Loan Owner
                                        </th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Monthly Repayment</th>
                                        <th class="text-center">Tenure</th>
                                        <th>Loan Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loans as $loan)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>{{$loan->user->name}}</td>
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
                                                {{$loan->emi()}}
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                {{$loan->due_date->diffInMonths($loan->created_at)}} Months ({{$loan->due_date->diffForHumans()}})
                                            </div>
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
                                            <a href="{{route('staff.loans.view', ['reference' => $loan->reference])}}" class="btn btn-primary">
                                                <i class="icon-eye"></i>
                                                View Loan
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            You have not received any loans yet
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
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js">

</script>
        <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    <script src="{{asset('assets/js/data-table.js')}}"></script>

<!--<script type="text/javascript" src="{{asset('assets/js/custom.js')}}"></script>-->
@endsection