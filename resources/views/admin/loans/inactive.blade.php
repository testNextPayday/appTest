@extends('layouts.admin-new')

@section('content')
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Cool-off Loans</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Reference #</th>
                                    <th>Borrower</th>
                                    <th>Payroll ID</th>
                                   <th>Staff</th>
                                    <th>Amount</th>
                                    
                                    <th class="text-center">Tenure</th>
                                    <th>Loan Status </th>
                                    <th>Manage</th>                                
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                @php
                                    $staff = $loan->collector_type == 'AppModelsUser' ? 'no staff' : $loan->collector;
                                    $user = $loan->user ?? null ;
                                @endphp
                                    <tr>
                                        <td><a href="{{route('admin.loans.view', ['reference' => $loan->reference])}}"
                                                    class="btn btn-outline-primary">{{$loan->reference}}</a></td>
                                        <td>{{ $user ? $user->name : 'No User' }}</td>
                                        <td>{{ $user ? optional($user->employments)->first()->payroll_id : ' No user'}}</td>
                                        <td>{{$staff->name ?? 'No staff'}}</td>
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
                                                class="btn btn-outline-primary">View</a>
                                               
                                            
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No Cool-off loans</td>
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


    <script src="{{asset('assets/js/data-table.js')}}">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
   
   
   
        
     
    
@endsection