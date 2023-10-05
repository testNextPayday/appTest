@extends('layouts.affiliates')

@section('page-css')
<style>
    td .badge {
        display: inline-block;
        width: 80%;
    }
</style>
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
               Withdrawal Requests
            </h4>
            <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#withdrawalRequestModal">
                <i class="fa fa-plus"></i> Place New
            </button>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 table-responsive">
                    <table id="order-listing" class="table">
                        <thead>
                            <tr>
                                <th>Reference #</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawalRequests as $request)
                                <tr>
                                    <td>{{ $request->reference}}</td>
                                    <td>₦ {{$request->amount}}</td>
                                    <td>{{$request->created_at->toDateString()}}</td>
                                    <td class="text-center">
                                        @if($request->status == 1)
                                            <span class="badge badge-primary">Pending</span>
                                        @elseif($request->status == 2)
                                            <span class="badge badge-info">Paid</span>
                                        @elseif($request->status == 3)
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @else
                                            <span class="badge badge-danger">Declined</span>
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
@endsection