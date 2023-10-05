@extends('layouts.staff-new')

@section('content')

    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Collection Logs</h4>
            </div>
            <p class="col-12 pt-3">Gives you collections due within specified period</p>
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
        
        @if($logs)
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Log Data</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Card Tries</th>
                                    <th>Loan</th>
                                    <th>Borrower</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <?php
                                        $loan = $log->loan;
                                        $user = $loan ? $loan->user : '';  
                                    ?>
                                    <tr>
                                        <td>{{ $log->payday}}</td>
                                        <td>
                                            @if($log->status)
                                                <span class="badge btn-block badge-success">
                                                    Paid<br/>
                                                    {{$log->date_paid}}
                                                </span>
                                            @else
                                                @if(now()->gte($log->payday))
                                                    <span class="badge badge-danger">
                                                        Due {{ now()->diffInDays($log->payday) }}
                                                    </span>
                                                @else
                                                    <span class="badge btn-block badge-primary">
                                                        Not Due
                                                    </span>
                                                @endif
                                                @if($log->order_issued)
                                                    <span class="ml-2 badge badge-info">
                                                        DDM Issued
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td> {{ $log->card_tries ?? 0 }} Card {{ str_plural('try', $log->card_tries ?? 0) }} </td>
                                        <td>
                                            <loan-display :loan="{{ $loan }}"></loan-display>
                                        </td>
                                        <td>
                                            <user-display :user="{{ $user }}"></user-display>
                                        </td>
                                        <td class="d-flex">
                                            @if($log->status)
                                                <button style="flex: 1" class="btn btn-block btn-success btn-xs">Paid</button>
                                            @else
                                                @if(now()->gte($log->payday))
                                                    @if(!$log->order_issued)
                                                        <form style="flex: 1" method="post" class="mr-2"
                                                            onsubmit="return confirm('Are you sure?')"
                                                            action="{{ route('admin.repayments.try-card', ['repaymentPlan' => $log->id]) }}">
                                                            {{ csrf_field() }}
                                                            <button class="btn btn-block btn-twitter btn-xs">Try Card</button>
                                                        </form>
                                                    @endif
                                                    <form style="flex: 1" method="post" class="mr-2"
                                                        onsubmit="return confirm('Are you sure?')"
                                                        action="{{ route('admin.repayments.use-ddm', ['repaymentPlan' => $log->id]) }}">
                                                        {{ csrf_field() }}
                                                        <button class="btn btn-block btn-google btn-xs">Use DDM</button>
                                                    </form>
                                                @else
                                                    <button style="flex: 1" class="btn btn-block btn-dark btn-xs">Not Due</button>
                                                @endif
                                                
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No Data</td>
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
    @if($logs)
        <script src="{{asset('assets/js/data-table.js')}}"></script>
    @endif
@endsection