@extends('layouts.admin-new')

@section('content')

    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Okra Log Data</h4>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center">
                <div class="card px-4 py-4">
                    <form action="" class="row align-items-center">
                        <div class="form-group col-sm-4">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date"/>
                        </div>
                        <div class="form-group col-sm-4">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="end_date"/>
                        </div>
                        <div class="col-sm-4">
                            <button class="btn btn-primary btn-sm">Pull Logs</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        @if($okraLogs)
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Log Data</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Borrower</th>
                                    <th>Loan Reference</th>
                                    <th>emi</th>
                                    <th>Amount Paid</th>
                                    <th>Month No.</th>
                                    <th>Plan Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($okraLogs as $okraLog)
                                    <tr>
                                        <td>{{ $okraLog->created_at}}</td>                                        
                                        <td>{{ $okraLog->user->name}}</td>
                                        <td>{{ $okraLog->loan->reference }}</td> 
                                        <td>{{ $okraLog->emi }}</td>
                                        <td>{{ $okraLog->amount_paid }}</td>
                                        <td>{{ $okraLog->repaymentPlan->month_no }}</td>
                                        <td>
                                            @if($okraLog->status)
                                                <label class="badge badge-primary">EMI Completed</label>
                                            @else
                                                <label class="badge badge-success">Incomplete</label>
                                            @endif
                                        </td>                                        
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Data Unavailable</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@section('page-js')
    @if($okraLogs)
        <script src="{{asset('assets/js/data-table.js')}}"></script>
    @endif
@endsection