@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.failed.transactions')}}">Failed Transactions</a></li>
        <li class="breadcrumb-item active">All</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">

            <table class="table table-responsive-sm table-hover table-outline mb-0" >

                <thead class="thead-light">

                    <tr>
                        <th>Title</th>
                        <th>Collection Method</th>
                        <th>#Reference</th>
                        <th>Amount</th>
                        <th>Status Text</th>
                        <th>Date</th>

                    </tr>

                </thead>

                <tbody>

                   @foreach($failed as $transaction)
                    <gateway-transaction  :transaction="{{$transaction}}" :showMore="{{json_encode('true')}}"></gateway-transaction>
                   @endforeach
                    
                </tbody>

            </table>
            
           
            <!--/.row-->
        </div>

        
    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection