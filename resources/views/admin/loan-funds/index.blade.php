@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">All Loan Funds</h4>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Owner</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                    <th>Request</th>
                                    <th>Loan</th>
                                    <th>Date Created</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($funds as $fund)
                                    @php
                                        $loanRequest = optional($fund->loanRequest);
                                        $loan = optional($loanRequest->loan);
                                    @endphp
                                    <tr>
                                        <td>{{ $fund->reference }}</td>
                                        <td><a href="{{route('admin.investors.view', ['investor'=> $fund->investor->reference])}}">{{ $fund->investor->name }}</a></td>
                                        <td>₦ {{number_format($fund->amount, 2) }}</td>
                                        <td>{{$fund->percentage }}</td>
                                        <td>
                                            <a href="{{route('admin.loan-requests.view', ['reference'=> $loanRequest->reference])}}" target="_blank">{{$loanRequest->reference}}</a>
                                        </td>
                                        <td>
                                            <a href="{{route('admin.loans.view', ['reference'=> $loan->reference])}}" target="_blank">{{$loan->reference}}</a>
                                        </td>
                                        <td>{{$fund->created_at->format('Y-m-d')}}</td>
                                        <td class="text-center">
                                            @if($fund->status == 1)
                                                <span class="badge badge-danger" href="#">
                                                    <small>Pending</small>
                                                </span>
                                            @elseif($fund->status == 2)
                                                <span class="badge badge-success" href="#">Active</span>
                                            @elseif($fund->status == 3)
                                                <span class="badge badge-warning" href="#">Cancelled</span>
                                            @elseif($fund->status == 4)
                                                <span class="badge badge-info" href="#">Up for Transfer</span>
                                            @elseif($fund->status == 5)
                                                <span class="badge badge-primary" href="#">Transfered</span>
                                            @elseif($fund->status == 6)
                                                <span class="badge badge-default" href="#">Fulfilled</span>
            
                                            @else
                                                <span class="badge badge-default" href="#">Unknown</span>
                                            @endif
                                        </td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Data Unavailable</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection