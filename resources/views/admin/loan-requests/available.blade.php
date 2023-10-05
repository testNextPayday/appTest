@extends('layouts.admin-new')

@section('content')
<div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Available Loan Requests</h4>
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
                                    <th>Request</th>
                                    <th>Duration</th>
                                    <th>Current EMI</th>
                                    <th>Date Created</th>
                                    <th>Assigned</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loanRequests as $loanRequest)
                                    <tr>
                                        <td>{{ $loanRequest->reference }}</td>
                                        <td>{{ $loanRequest->user->name }}</td>
                                        <td>₦ {{$loanRequest->amount}} at <span>{{$loanRequest->interest_percentage}}%</span> Interest</td>
                                        <td>{{$loanRequest->duration}} {{ str_plural('Month', $loanRequest->duration) }}</td>
                                        <td>₦ {{number_format($loanRequest->emi(), 2)}}</td>
                                        <td>{{$loanRequest->created_at->format('Y-m-d')}}</td>
                                        <td>
                                            @if($investor = $loanRequest->investor)
                                                Assigned: 
                                                <a href="{{route('admin.investors.view', ['investor' => $investor->reference])}}">
                                                    {{ $investor->reference }}
                                                </a>
                                            @else
                                                Not Assigned
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