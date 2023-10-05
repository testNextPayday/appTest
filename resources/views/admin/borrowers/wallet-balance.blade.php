@extends('layouts.admin-new')

@section('content')

    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Wallet Balance</h4>
                <div class="col-sm-3" style="display:inline-block">
                    <form action="" method="get">
                        <input class="form-control" name="q" placeholder="Enter name, email or reference" value="{{$searchTerm}}" required/>
                    </form>
                </div>    
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Reference #</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Payroll ID</th>
                                    <th>Loan Wallet</th>
                                    <th>Wallet Balance</th>
                                    <th>Escrow Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                        @foreach($users as $bit)
                                            <tr>
                                                <td>{{ $bit->reference}}</td>
                                                <td>{{ $bit->name}}</td>
                                                <td>{{ $bit->email}}</td>
                                                <td>{{ optional($bit->employments()->first())->payroll_id }}</td>                                                
                                                <td>₦{{ number_format($bit->masked_loan_wallet, 2)}}</td>                                              
                                                <td>₦{{ number_format($bit->wallet, 2)}}</td>
                                                <td>₦{{ number_format($bit->escrow, 2)}}</td>
                                                <td>
                                                    @if($bit->is_active)
                                                        <label class="badge badge-success">Active</label>
                                                    @else
                                                        <label class="badge badge-danger">Inactive</label>
                                                    @endif
                                                </td>
                                                <td>                                                   
                                                    <a href="{{route('admin.users.view', ['user' => $bit->reference])}}"
                                                        class="btn btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                        <td colspan="5" class="text-right">
                                            {{$users->links()}}
                                        </td>
                                    </tr>
                                        
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
