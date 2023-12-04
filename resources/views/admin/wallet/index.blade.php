@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Wallet Reports</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Owner</th>
                                    <th>Transaction Type</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                        <td>{{ $transaction->owner->name ?? 'No name' }}</td>
                                        <td class="{{ ($transaction->direction == 1) ? "text-success" : "text-danger" }}">{{ ($transaction->direction == 1) ? "Credit" : "Debit" }}</td>
                                        <td>{{ $transaction->description}}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                        
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Data Unavailable</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            
                        </table>

                        <tfoot>
                            <h4>Pages</h4>

                            <div>{{$transactions->links()}}</div>
                        </tfoot>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection