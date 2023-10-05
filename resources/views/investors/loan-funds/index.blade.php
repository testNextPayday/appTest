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
            <h4 class="page-title">Given Funds</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Given Funds</h4>
                    </div>
                    <div class="table-responsive support-pane no-wrap">
                        @if(count($funds))
                        @foreach($funds as $fund)
                        @php
                          $request = $fund->loanRequest;
                          
                        @endphp
                        <div class="t-row">
                            <div class="tumb">
                                <div class="img-sm rounded-circle bg-info d-flex align-items-center justify-content-center text-white">
                                    LF
                                </div>
                            </div>
                            <div class="content">
                                <p class="font-weight-bold mb-2 d-inline-block">{{ @optional($request)->user->reference }}</p>
                                <p class="text-muted mb-2 d-inline-block">{{ $fund->created_at->format('Y-m-d') }}</p>
                                <p>
                                  Loan duration is <strong>{{ optional($request)->duration }} {{str_plural('month', optional($request)->duration)}}</strong>
                                  &nbsp;
                                  @if($fund->status == 1)
                                  <span class="badge badge-warning">Pending</span>
                                  @elseif($fund->status == 2)
                                  <span class="badge badge-info">Active</span>
                                  @elseif($fund->status == 3)
                                  <span class="badge badge-secondary">Cancelled</span>
                                  @elseif($fund->status == 4)
                                  <span class="badge badge-primary">On transfer</span>
                                  @elseif($fund->status == 5)
                                  <span class="badge badge-success">Transferred</span>
                                  @else
                                  <span class="badge badge-success">Fulfilled</span>
                                  @endif
                                  
                                  &nbsp;
                                    <a class="badge badge-success"
                                        href="{{ route('investors.funds.show', ['loanFund' => $fund->reference]) }}">
                                        View Fund
                                    </a>
                                </p>
                            </div>
                            <div class="tile">
                                <p class="text-muted mb-2">Request REF</p>
                                <p class="font-weight-bold">{{ strtoupper(optional($request)->reference) }}</p>
                            </div>
                            <div class="tile">
                                <p class="text-muted mb-2">Fund Amount</p>
                                <p class="font-weight-bold">₦ {{ number_format($fund->amount, 2) }}</p>
                            </div>
                            <div class="tile">
                                <p class="text-muted mb-2">Monthly Return</p>
                                @if(isset($request))
                                <p class="font-weight-bold">₦ {{@number_format($fund->newEmi(),2)}}</p>
                                @endif
                               
                            </div>
                        </div>
                        @endforeach
                        @else
                        
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="mb-0 d-none d-md-block"></p>
                        <nav>
                          {{$funds->links('layouts.pagination.default')}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
