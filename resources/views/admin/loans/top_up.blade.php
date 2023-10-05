@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Eligible for Top up Loans</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Reference #</th>
                                    <th>Borrower</th>
                                    <th>Amount</th>
                                    <th class="text-center">Tenure</th>
                                    <th>Loan Status</th>
                                    <th>Manage</th>                                
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                    <tr>
                                        <td><a href="{{route('admin.loans.view', ['reference' => $loan->reference])}}"
                                                    class="btn btn-outline-primary">{{$loan->reference}}</a></td>
                                        <td>{{ $loan->user->name}}</td>
                                        <td>₦{{ number_format($loan->amount, 2)}}</td>
                                        <td>
                                            {{$loan->due_date->diffInMonths($loan->created_at)}} Months ({{$loan->due_date->diffForHumans()}})
                                        </td>
                                        <td>
                                            @component('components.admin-loan-status', ['loan' => $loan])
                                            @endcomponent
                                            @if($loan->is_top_up)
                                            <a class='btn btn-outline-primary'>Top up</a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('admin.loans.view', ['reference' => $loan->reference])}}"
                                                class="btn btn-outline-primary">View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No Top up loans</td>
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