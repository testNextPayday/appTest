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
                                        <th>Amount</th>
                                        <th>Approval Status</th>
                                        <th>Payment Ref.</th>
                                        <th>Payment Type</th>                                        
                                        <th>Created Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse($promissoryInvestment as $fund)
                                       
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>                                            
                                            <td>{{number_format($fund->amount, 2)}}</td>                                                                                       
                                            <td>
                                                @if($fund->approval_status == 1)
                                                    <span class="badge badge-success">Approved</span>
                                                @endif

                                                @if($fund->approval_status != 0)
                                                    <span class="badge badge-danger">Pending</span>
                                                @endif

                                            </td>
                                            <td>{{$fund->reference}}</td>
                                            <td>{{$fund->investment_type}}</td>                                             
                                            <td>{{$fund->created_at}}</td>
                                            <td>@if($fund->verification_status == 'active')
                                                    <span class="badge badge-success">Approved</span>
                                                @else
                                                    <form action="{{route('promissory-note.verifystatus')}}" method="post">
                                                        <input type="hidden" name="reference" value="{{$fund->reference}}">
                                                        <input type="submit" class="btn btn-success" value="Verify">
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