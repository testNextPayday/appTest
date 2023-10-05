@extends('layouts.investor')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Collections</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">My Collections</h4>
                    </div>
                    <div class="table-responsive support-pane no-wrap" >
                        @if(count($collections))
                        @foreach($collections as $collection)
                    
                        <div class="t-row">
                            <div class="tumb">
                                <div class="img-sm rounded-circle bg-info d-flex align-items-center justify-content-center text-white">
                                    LC
                                </div>
                            </div>
                            <div class="content">
                                <!-- <p class="font-weight-bold mb-2 d-inline-block">{{ $collection->user->name }}</p> -->
                                <p class="text-muted mb-2 d-inline-block">{{ $collection->created_at->format('l jS \\of F Y') }}</p>
                                <p>
                                    <a class="badge badge-success"
                                        href="{{ route('investors.funds.show', ['loanFund' => $collection->fund->reference]) }}">
                                        View Fund
                                    </a>
                                </p>
                            </div>

                            <div class="tile">
                                <p class="text-muted mb-2">Fund REF</p>
                                <p class="font-weight-bold">{{ strtoupper($collection->fund->reference) }}</p>
                            </div>

                            <div class="tile">
                                <p class="text-muted mb-2">Repayment Amount</p>
                                <p class="font-weight-bold">₦ {{ number_format($collection->amount, 2) }}</p>
                            </div>
                            
                            <div class="tile">
                                <p class="text-muted mb-2">Commission</p>
                                <p class="font-weight-bold">₦ {{number_format($collection->commission, 2)}}</p>
                            </div>
                        </div>
                        @endforeach
                        @else
                        
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <p class="mb-0 d-none d-md-block"></p>
                        <nav>
                          {{$collections->links('layouts.pagination.default')}}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content-wrapper ends -->
@endsection
@section('page-js')
    <script src="{{asset('assets/js/data-table.js')}}"></script>
@endsection
