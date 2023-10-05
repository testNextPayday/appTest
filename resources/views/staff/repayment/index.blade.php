@extends('layouts.staff-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Loan Repayments</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Loan</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Payday</th>
                                    <th>Collection date</th>
                                    <th>Status</th>
                                    <th>Reciept</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)

                                   
                                    <tr>
                                        <td>{{ $plan->loan->user->name }}</td>
                                        <td>{{$plan->loan->reference }}</td>
                                    
                                        <td>{{ $plan->collection_mode == null ? 'No collection' : $plan->collection_mode  }}</td>
                                        
                                        <td>{{ number_format($plan->balance, 2) }}</td>
                                        <td>{{$plan->payday}}</td>
                                        <td>{{$plan->date_paid}}</td>
                                        <td>
                                            @if($plan->status)
                                                <a class="badge badge-success" href="#">Paid</a>
                                            @else
                                                <a class="badge badge-warning" href="#">Not Paid</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($plan->status)
                                                <a class="label label-success" href="#">Paid</a>
                                            @else
                                                
                                                @if($plan->payday <= Carbon\Carbon::today() && $plan->status)
                                                    <a class="badge badge-success" href="#">Paid</a>
                                                @else
                                                    <a class="badge badge-info" href="#">Not Due</a>
                                                @endif
                                            @endif
                                            </div>
                                        </td>
                                        <td>{{$plan->status_message}}</td>
                                    </tr>
                                   

                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Data Unavailable</td>
                                    </tr>
                                   
                                @endforelse
                            </tbody>
                        </table>
                        <tfoot>
                                <h4>Pages</h4>
                                <div>{{$plans->links()}}</div>
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