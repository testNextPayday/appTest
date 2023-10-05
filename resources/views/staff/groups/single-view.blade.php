@extends('layouts.staff-new')

@section('content')

    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Users</h4>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th> #</th>
                                    <th>Reference #</th>
                                    <th>Name</th> 
                                    <th>Loan Wallet</th>                                   
                                    <th>Wallet Balance</th>
                                    <th>Escrow Balance</th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $loop->index+1}}</td>
                                        <td>{{ $user->reference}}</td>
                                        <td>{{ $user->name}}</td>                                        
                                        <td>₦{{ number_format($user->loan_wallet, 2)}}</td>
                                        <td>₦{{ number_format($user->wallet, 2)}}</td>
                                        <td>₦{{ number_format($user->escrow, 2)}}</td>
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