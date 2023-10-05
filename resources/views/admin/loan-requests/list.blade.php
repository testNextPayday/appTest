@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">{{ $title }}</h4>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Request Date</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Interest %</th>
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loanRequests as $loanRequest)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $loanRequest->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $loanRequest->reference }}</td>
                                        <td>₦ {{ number_format($loanRequest->amount, 2) }}</td>
                                        <td>{{ $loanRequest->interest_percentage }}%</td>
                                    
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