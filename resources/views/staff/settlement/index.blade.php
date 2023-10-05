@extends('layouts.staff-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Settlements</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Reference #</th>
                                    <th>Amount</th>
                                    <th>Loan Reference</th>
                                    <th>Manage</th>                                
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settlements as $settlement)
                                    <tr>
                                       <td>{{$settlement->reference}}</td>
                                       <td>{{number_format($settlement->amount,2)}}</td>
                                       <td>{{$settlement->loan->reference}}</td>
                                       
                                       
                                        <td>
                                            <a href="{{route('staff.settlement.view', ['reference' => $settlement->reference])}}"
                                                class="btn btn-outline-primary">View</a>
                                            
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No settlement</td>
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