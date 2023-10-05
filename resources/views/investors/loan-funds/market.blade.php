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
            <h4 class="page-title">Investments on sale</h4>
        </div>
    </div>
    <div class="row">
        @if(count($funds))
        @foreach($funds as $fund)
        @php
            $request = $fund->loanRequest;
        @endphp
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card text-center">
                <div class="card-body">
                    <small class="text-left">{{strtoupper($fund->reference)}}</small>
                    <h4>
                        ₦ {{ number_format($fund->sale_amount, 2) }}
                    </h4>
                    <small> CURRENT VALUE: ₦ {{ number_format($fund->currentValue, 2) }}</small>
                    
                    <br/><br/>
                    <p class="text-muted">
                        Listed by <strong>{{$fund->investor->reference}}</strong> with a remainder of
                        <strong>{{ $fund->timeLeft }} {{str_plural('month', $fund->timeLeft)}}</strong>
                    </p>
                    
                    <bid :fund="{{json_encode($fund)}}"
                        :current-value="{{json_encode($fund->currentValue)}}"
                        :bidders="{{json_encode($fund->bidders)}}"
                        :investor-id="{{json_encode(auth()->id())}}"
                        :potential-gain="{{json_encode($fund->potential_gain)}}"></bid>
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-12">
                                <h6>Monthly Return</h6>
                                <p>
                                    {{number_format($request->monthlyPayment($request->amount),2)}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endforeach
        <div class="col-12 grid-margin">
            {{$funds->links('layouts.pagination.default')}} 
        </div>
        
        @else
        <p class="text-center">No items available</p>
        @endif
    </div>
</div>
<!-- content-wrapper ends -->
@endsection


