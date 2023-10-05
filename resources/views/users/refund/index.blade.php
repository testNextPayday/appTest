@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Refund Requests</a></li>
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
                            Refund Requests
                            <span class="pull-right">
                                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#refundRequest">New Request (Available : ₦{{number_format(Auth::user()->masked_loan_wallet, 2)}})</button>
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Status</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($refunds as $refund)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                ₦ {{$refund->amount}}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$refund->created_at->toDateString()}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-center text-muted">
                                                @if($refund->status == 1)
                                                Approved
                                                @elseif($refund->status == 2)
                                                Rejected
                                                @elseif($refund->status == 0)
                                                Pending
                                                @else
                                                Pending
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <div>{{$refund->reason}}</div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            You have not made any Refund request yet
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
    
    <div class="modal fade" id="refundRequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-primary modal-xs" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">New Refund Request (Available : ₦{{number_format(Auth::user()->loan_wallet, 2)}})</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{route('users.refund.request') }}">

                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}">
                                    <div class="modal-body">
                                    
                                        <div class="card-body">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                <label>Amount</label>
                                                <input type="number" class="form-control" name="amount" 
                                                   
                                                    placeholder="Enter an amount in Naira">
                                            </div>

                                            <div class="form-group">
                                                <label>Select Loan :</label>
                                                <select name="loan_id" class="form-control">
                                                    <option value="null">Choose a loan </option>
                                                    @foreach(Auth::user()->receivedLoans as $loan)
                                                        <option value="{{$loan->id}}">{{$loan->reference}} ( ₦{{$loan->amount}})</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Give a Refund Reason</label>
                                                <textarea class="form-control" name="reason" rows="10">
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Request Refund</button>
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