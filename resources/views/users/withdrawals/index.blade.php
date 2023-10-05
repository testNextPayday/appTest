@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Withdrawal Requests</a></li>
        <li class="breadcrumb-item active">All</li>

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
                            Withdrawal Requests 
                            <span class="pull-right">
                                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#withdrawalRequestModal">New Request</button>
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Reference</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($withdrawalRequests as $request)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$request->reference}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                ₦ {{$request->amount}}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$request->created_at->toDateString()}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-center text-muted">
                                                @if($request->status == 1)
                                                Pending
                                                @elseif($request->status == 2)
                                                Paid
                                                @elseif($request->status == 3)
                                                Cancelled
                                                @else
                                                Declined
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            You have not made any withdrawal request yet
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
    
    <div class="modal fade" id="withdrawalRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-xs" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Withdrawal Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="POST" action="{{route('users.withdrawals.request') }}">
                <div class="modal-body">
                    <div class="card-body">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" 
                                value="{{old('amount')"
                                placeholder="Enter an amount in Naira">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Request Withdrawal</button>
                </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</main>
@endsection

@section('page-js')
@endsection