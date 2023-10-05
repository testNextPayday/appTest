@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Loan Requests Ready for Setup</h4>
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
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Interest %</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loanRequests as $loanRequest)
                                    <tr>
                                        <td>{{ $loanRequest->reference }}</td>
                                        <td>{{$loanRequest->user ? $loanRequest->user->name : '########'}}</td>
                                        <td>₦ {{ number_format($loanRequest->amount, 2) }}</td>
                                        <td>{{ $loanRequest->interest_percentage }}%</td>
                                    
                                        <td>{{$loanRequest->created_at->format('Y-m-d')}}</td>
                                        <td>
                                           @if($loanRequest->status == 4)
                                                <span class="badge badge-info">Awaiting Setup</span>
                                            @else
                                                <span class="badge badge-danger">Unknown</span>
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
                                        <td colspan="6" class="text-center">Data Unavailable</td>
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