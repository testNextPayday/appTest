@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Transactions</a></li>
        <li class="breadcrumb-item active">Wallet</li>

        <li class="breadcrumb-menu">
          <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-primary btn-lg waves-effect text-white" href="{{route('users.loan-requests.create')}}" style="border-radius: 20px;"> 
             <span style="font-size: 0.9rem;"> <i class="icon-cursor text-white"></i> Get Loan</span>
            </a>
          </div>
        </li>
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
                            Wallet Transaction
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th class="text-center">Direction</th>
                                        <th class="text-center">Description</th>
                                        <th class="">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$transaction->reference}}</div>
                                        </td>
                                        <td>
                                            <div>₦ {{$transaction->amount}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$transaction->direction == 1 ? 'Incoming' : 'Outgoing'}}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$transaction->description}}
                                            </div>
                                        </td>
                                        <td>{{ $transaction->created_at->diffForHumans()}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            You have not made any transaction yet
                                        </td>
                                    </tr>
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
@endsection