@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
               {{ isset($title) ? $title : 'Withdrawal Requests' }}
            </h4>
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
                                <th>Name</th>
                               
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-center">Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawalRequests as $request)
                                <?php
                                    $requester = $request->requester;
                                    $url = "#";
                                    $getClass = $request->requester ? get_class($requester) : "App\Models\User";
                                        switch($getClass) {
                                            case "App\Models\User":
                                                $url = route('admin.users.view', ['user' => $requester?->reference]);
                                                break;
                                            case "App\Models\Investor":
                                                $url = route('admin.investors.view', ['investor' => $requester?->reference]);
                                                break;
                                            case "App\Models\Affiliate":
                                                $url = route('admin.affiliates.show', ['affiliate' => $requester?->reference]);
                                                break;
                                        }

                                ?>
                                <tr>
                                    <td>{{ $request->reference}}</td>
                                    <td> <a  href="{{ $url }}">
                                        {{ $requester?->name}}
                                        </a></td>
                                   
                                    <td>₦ {{number_format($request->amount, 2)}}</td>
                                    <td>{{$request->created_at}}</td>
                                    <td>
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
                                    <td>
                                        @if($request->status == 1)
                                            <a class="btn btn-xs btn-success" 
                                                href="{{route('admin.withdrawal-requests.approve', ['request_id' => $request->id ])}}"
                                                onclick="return confirm('Are you sure you want to approve this request?');">
                                                <i class="icon-action-redo"></i> Approve</a>
                                            <a class="btn btn-xs btn-warning" 
                                                href="{{route('admin.withdrawal-requests.approve-backend', ['request_id' => $request->id ])}}"
                                                onclick="return confirm('Are you sure you want to approve this request backend?');">
                                                <i class="icon-action-redo"></i> Approve (Backend)</a>
                                            <a class="btn btn-xs btn-danger"
                                                href="{{route('admin.withdrawal-requests.decline', ['request_id' => $request->id ])}}"
                                                onclick="return confirm('Are you sure you want to decline this request?');">
                                                <i class="icon-close"></i> Decline</a>
                                        @elseif($request->status == 2)
                                            <button class="btn btn-xs btn-success"><i class="icon-check"></i> Paid</button>
                                        @elseif($request->status == 3)
                                            <button class="btn btn-xs btn-default"><i class="icon-arrow-down-circle"></i> Cancelled</button>
                                        @else
                                            <button class="btn btn-xs btn-danger"><i class="icon-ban"></i> Declined</button>
                                        @endif
                                        <a class="btn btn-xs btn-outline-info" href="{{route('admin.withdrawal-requests.show', ['request_id'=> $request->id])}}">
                                            <i class="icon-action-redo"></i> View Request
                                        </a>
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