@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">All Loan Requests</h4>
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
                                    <th>Duration</th>
                                    <th>Date Created</th>
                                    <th class="text-center">Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loanRequests as $loanRequest)
                                    <tr>
                                        <td>{{ $loanRequest->reference }}</td>
                                        <td>{{ $loanRequest->user->name }}</td>
                                        <td>₦ {{number_format($loanRequest->amount, 2) }}</td>
                                        <td>{{$loanRequest->duration}} {{ str_plural('Month', $loanRequest->duration) }}</td>
                                        <td>{{$loanRequest->created_at->format('Y-m-d')}}</td>
                                        <td class="text-center">
                                            @if($loanRequest->status == 0)
                                                <span class="badge badge-danger" href="#">
                                                    <small>Pending Employee <br/>Verification</small>
                                                </span>
                                            @elseif($loanRequest->status == 1)
                                                <span class="badge badge-warning" href="#">
                                                    <small>Pending Admin <br/>Activation</small>
                                                </span>
                                            @elseif($loanRequest->status == 2)
                                                <span class="badge badge-success" href="#">Active</span>
                                            @elseif($loanRequest->status == 3)
                                                <span class="badge badge-warning" href="#">Cancelled</span>
                                            @elseif($loanRequest->status == 4)
                                                <span class="badge badge-info" href="#">Taken</span>
                                            @elseif($loanRequest->status == 5)
                                                <span class="badge badge-danger" href="#">Verification Declined</span>
                                            @elseif($loanRequest->status == 6)
                                                <span class="badge badge-warning" href="#">Activation Declined</span>
                                            @elseif($loanRequest->status == 7)
                                            <span class="badge badge-warning" href="#">Request Referred </span>
                                            @else
                                                <span class="badge badge-default" href="#">Unknown</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-primary"
                                                href="{{route('admin.loan-requests.view', ['reference' => $loanRequest->reference])}}">
                                                View
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
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection