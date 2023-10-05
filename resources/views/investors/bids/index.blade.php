@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">My Bids</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">My Bids</h4>
                    </div>
                    <div class="table-responsive support-pane no-wrap">
                        @if(count($bids))
                        @foreach($bids as $bid)
                        <div class="t-row">
                            <div class="tumb">
                                <div class="img-sm rounded-circle bg-info d-flex align-items-center justify-content-center text-white">
                                    BD
                                </div>
                            </div>
                            <div class="content">
                                <p class="font-weight-bold mb-2 d-inline-block">{{ strtoupper($bid->loanFund->reference) }}</p>
                                <p class="text-muted mb-2 d-inline-block"><small>&nbsp;FUND REF</small></p>
                                <p>
                                  Bid placed on {{ $bid->created_at->format('l jS \\of F Y') }} for {{ strtoupper($bid->loanFund->reference) }}
                                </p>
                            </div>
                            <div class="tile">
                                <p class="text-muted mb-2">Amount</p>
                                <p class="font-weight-bold">₦ {{ number_format($bid->amount, 2) }}</p>
                            </div>
                            <div class="tile">
                                <p class="text-muted mb-2">Status</p>
                                <p class="font-weight-bold">
                                    @if($bid->status == 1)
                                        <span class="badge badge-secondary">Pending</span>
                                    @elseif($bid->status == 2)
                                        <span class="badge badge-success">Accepted</span>
                                    @elseif($bid->status == 3)
                                        <span class="badge badge-warning">Cancelled</span>
                                    @else
                                        <span class="badge badge-danger">Declined</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="t-row text-center">
                            <h4>You've placed no bids yet</h4>
                        </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="mb-0 d-none d-md-block"></p>
                        <nav>
                          {{$bids->links('layouts.pagination.default')}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
