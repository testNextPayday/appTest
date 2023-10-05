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
            <h4 class="page-title">Active Loan Requests</h4>
        </div>
    </div>
    <div class="row">
        @if(count($loanRequests))
        @foreach($loanRequests as $loanRequest)

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card text-center">
                <div class="card-body">
                    <small class="text-left">{{strtoupper($loanRequest->reference)}}</small>
                    <h4>
                        ₦ {{ number_format($loanRequest->amount, 2) }}
                        @ {{ $loanRequest->interest_percentage}}%
                    </h4>
                    <p class="text-muted">
                        by {{$loanRequest->user->reference}} for
                        {{ $loanRequest->duration }} {{str_plural('month', $loanRequest->duration)}}
                    </p>
                    <p class="mt-4 card-text">
                        {{ $loanRequest->comment}}
                    </p>
                        
                    @php
                        $employment = $loanRequest->employment()->with('employer')->first();  
                    @endphp

                      <invest 
                        :loan-request="{{$loanRequest}}"
                        :investors="{{$loanRequest->investors()}}"
                        :investor-id="{{auth()->id()}}" 
                        :repayment="{{$loanRequest->repayment}}"></invest>
                     <employment-display :employment="{{$employment ?? 0 }}" ></employment-display>
                    
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-6">
                                <h6>Monthly Return</h6>
                                <p>
                                    {{number_format($loanRequest->monthlyPayment($loanRequest->amount),2)}}
                                </p>
                            </div>
                            <div class="col-6">
                                <h6>Risk Rating</h6>
                                <p>
                                    @for($i=1; $i<=5; $i++)
                                        @if($loanRequest->risk_rating >= $i)
                                            <i class="icon-star checked"></i>
                                        @else
                                            <i class="icon-star"></i>    
                                        @endif
                                    @endfor
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endforeach
        <div class="col-12 grid-margin">
            {{$loanRequests->links('layouts.pagination.default')}} 
        </div>
        
        @else
        <p class="text-center">No requests available</p>
        @endif
    </div>
</div>
<!-- content-wrapper ends -->
@endsection


