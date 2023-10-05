@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Bids</li>
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
                            Bids placed on loans
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-people"></i></th>
                                        <th>Owner</th>
                                        <th>Owner's Offer</th>
                                        <th class="text-center">My Offer</th>
                                        <th>Account</th>
                                        <th>Bid Date</th>
                                        <th class="text-center">Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bids as $bid)
                                    <tr>
                                        <td class="text-center">
                                            <div class="avatar">
                                                <img src="{{$bid->loanFund->investor->avatar}}" class="img-avatar" alt="Avatar">
                                                <span class="avatar-status badge-success"></span>
                                             </div>
                                        </td>
                                        <td>
                                            <div>
                                                {{$bid->loanFund->investor->name}}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                 ₦{{$bid->loanFund->sale_amount}}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                ₦ {{$bid->amount}}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                                {{$bid->investor->name}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$bid->created_at->toDateString()}}
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                @if($bid->status == 1)
                                                <?php 
                                                    $remainder = $bid->loanFund->loanRequest->loan->repaymentPlans()->whereStatus(false);
                                                ?>
                                                <loan-bid-update :bid="{{$bid}}" 
                                                        :loan="{{$bid->loanFund}}" 
                                                        :url="'{{route('staff.bids.loans.update')}}'"
                                                        :loan-value="{{$remainder->sum('interest') + $remainder->sum('principal')}}">
                                                </loan-bid-update>
                                                <a class="btn btn-xs btn-danger"
                                                    onclick="return confirm('Are you sure?');"
                                                    href="{{route('staff.bids.cancel', ['bid_id' => encrypt($bid->id)])}}">
                                                    Cancel Offer
                                                </a>
                                                @elseif($bid->status == 2)
                                                <a class="btn btn-xs btn-success" href="#">Accepted</a>
                                                @elseif($bid->status == 3)
                                                <a class="btn btn-xs btn-danger" href="#">Rejected</a>
                                                @elseif($bid->status == 4)
                                                <a class="btn btn-xs btn-default" href="#">Cancelled</a>
                                                @else
                                                <a class="btn btn-xs btn-default" href="#">Not Available</a>
                                                @endif
                                            
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            You've not placed any bids yet
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