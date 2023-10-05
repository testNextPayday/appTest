@extends('layouts.investor')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Payday Notes</a></li>
        <li class="breadcrumb-item active">Active</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            @if ($errors->any())
            <div class="row justify-content-center">
                <div class="col-sm-6">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Promissory Notes
                        </div>
                        <div class="card-body">
                            <table  id="order-listing" class="table table-responsive table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><i class="icon-credit-card"></i></th>
                                        <th>Investors Name</th>
                                        <th>Reference</th>
                                        <th>Amount</th>                                       
                                        <th>Payment Type</th>
                                        <th>Verification Status</th>                                        
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse($payments as $payment)                                       
                                        <tr>                                            
                                            <td>{{$loop->index + 1}}</td>    
                                            <td>{{$payment->investor_name}}</td>                                        
                                            <td>{{$payment->investor_reference}}</td>
                                            <td>{{number_format($payment->amount, 2)}}</td>                                            
                                            <td>{{$payment->investment_type}}</td>                                             
                                            <td>{{$payment->created_at}}</td>
                                            <td>@if($payment->verification_status == 'active')
                                                    <span class="badge badge-success">Successful</span>
                                                @else
                                                <form action="{{route('admin.promissory-note.verifymonostatus')}}" method="post">
                                                        <input type="hidden" name="reference" value="{{$fund->reference}}">
                                                        <input type="submit" class="btn btn-success" value="Verify">
                                                </form>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->status == 1)
                                                    <span class="badge badge-warning">Approved</span>
                                                @else
                                                    <form action="{{route('admin.promissory-note.approve.fund')}}" method="post">
                                                            <input type="hidden" name="status" value="1">
                                                            <input type="hidden" name="amount" value="{{$payment->amount}}">
                                                            <input type="hidden" name="investor_id" value="{{$payment->investor_id}}">
                                                            <input type="submit" class="btn btn-success" value="Approve Payment">
                                                    </form>
                                                @endif
                                                
                                                
                                            </td>
                                        </tr>
                                    @empty
                                        <p>No payday funds available yet</p>
                                    @endforelse
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')

    <script src="{{asset('assets/js/data-table.js')}}">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
   
@endsection