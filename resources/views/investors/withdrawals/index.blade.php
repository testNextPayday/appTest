@extends('layouts.investor')

@section('page-css')
    <style>
        .checked {
            color: orange;
        }
    </style>
@endsection

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Withdrawal Requests</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Withdrawal Requests</h4>
                        <div class="btn-toolbar mb-0 d-none d-sm-block" role="toolbar" aria-label="Toolbar with button groups">
                            <div class="btn-group" role="group" aria-label="First group">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#requestModal">
                                    <i class="mdi mdi-plus-circle"></i> Place New
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive support-pane no-wrap">
                        @if(count($withdrawals))
                        @foreach($withdrawals as $withdrawal)
                        <div class="t-row">
                            <div class="tumb">
                                <div class="img-sm rounded-circle bg-info d-flex align-items-center justify-content-center text-white">
                                    WR
                                </div>
                            </div>
                            <div class="content">
                                <p class="font-weight-bold mb-2 d-inline-block">{{ strtoupper($withdrawal->reference) }}</p>
                                <p class="text-muted mb-2 d-inline-block"><small>&nbsp;REF</small></p>
                                <p>
                                  Withdrawal Request placed on {{ $withdrawal->created_at->format('l jS \\of F Y') }}
                                </p>
                            </div>
                            <div class="tile">
                                <p class="text-muted mb-2">Amount</p>
                                <p class="font-weight-bold">₦ {{ number_format($withdrawal->amount, 2) }}</p>
                            </div>
                            <div class="tile">
                                <p class="text-muted mb-2">Status</p>
                                <p class="font-weight-bold">
                                    @if($withdrawal->status == 1)
                                        <span class="badge badge-secondary">Pending</span>
                                    @elseif($withdrawal->status == 2)
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($withdrawal->status == 3)
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
                            <h4>You've made no withdrawal requests yet</h4>
                        </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="mb-0 d-none d-md-block"></p>
                        <nav>
                          {{$withdrawals->links('layouts.pagination.default')}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Request Modal Starts -->
        <div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-3" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel-3">Place a withdrawal request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <withdrawal-request-form :url="'{{route('investors.withdrawals')}}'" :limit="{{$withdrawal_limit}}" :token="'{{csrf_token()}}'" :wallet="{{auth('investor')->user()->wallet}}"></withdrawal-request-form>
                       
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="requestForm" class="btn btn-xs btn-success">
                            Submit Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Request Modal Ends -->
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
