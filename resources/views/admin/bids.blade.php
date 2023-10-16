@extends('layouts.admin')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('users.bids.index')}}">Bids</a></li>
        <li class="breadcrumb-item active">All Bids</li>
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
                            My Bids
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-people"></i></th>
                                        <th>Owner</th>
                                        <th>Owner's Offer</th>
                                        <th>Bidder</th>
                                        <th>Bidder's Offer</th>
                                        <th>Bid Type</th>
                                        <th>Bid Date</th>
                                        <th class="text-center">Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bids as $bid)
                                    <tr>
                                        <td >
                                            <div class="avatar">
                                                <img src="{{asset('coreui/img/avatars/1.jpg')}}" class="img-avatar" alt="admin@bootstrapmaster.com">
                                                <span class="avatar-status badge-success"></span>
                                             </div>
                                        </td>
                                        <td>
                                            @if($bid->entity == 1)
                                            <div>{{$bid->loanRequest->user->lastname}} {{$bid->loanRequest->user->firstname}}</div>
                                            @else
                                            <div>{{$bid->loan->sender->lastname}} {{$bid->loan->sender->firstname}}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                @if($bid->entity == 1)
                                               ₦{{$bid->loanRequest->amount}} at <span>{{$bid->loanRequest->interest_percentage}}%</span> Interest
                                                @else
                                                ₦{{$bid->loan->amount}}
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td>
                                           <div>{{$bid->user->lastname}} {{$bid->user->firstname}}</div>
                                        </td>
                                        
                                        <td >
                                            <div class="small text-muted">
                                               @if($bid->entity == 1) 
                                                ₦ {{$bid->amount}} at <span>{{$bid->interest_percentage}}%</span> Interest
                                                @else
                                                ₦ {{$bid->amount}}
                                                @endif
                                            </div>
                                        </td>
                                        <td >
                                            <div class="small text-muted">
                                               @if($bid->entity == 1) 
                                                Loan Request
                                                @else
                                                Loan Transfer
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$bid->created_at}}
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                @if($bid->entity == 1)
                                                @if($bid->status == 1 && $bid->loanRequest->status == 1)
                                                <a class="btn btn-xs btn-warning" href="#">Pending Acceptance</a>
                                                @elseif($bid->status == 2)
                                                <a class="btn btn-xs btn-success" href="#">Accepted</a>
                                                @elseif($bid->status == 3)
                                                <a class="btn btn-xs btn-danger" href="#">Rejected</a>
                                                @elseif($bid->status == 4)
                                                <a class="btn btn-xs btn-default" href="#">Cancelled</a>
                                                @else
                                                <a class="btn btn-xs btn-default" href="#">Not Available</a>
                                                @endif
                                                @else
                                                Loan Bid Actions
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            No Bids Available Right Now!
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