@extends('layouts.affiliates')

@section('content')
<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">My Loan Requests</h4>
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">
        @if(count($loanRequests))
        @foreach($loanRequests as $loanRequest)

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card text-center">
                <div class="card-body">
                    <small class="text-left">{{strtoupper($loanRequest->reference)}}</small>
                    <h4>
                        <a href="{{route('affiliates.loan-requests.show', ['loanRequest' => $loanRequest->reference])}}">
                            
                            ₦ {{ number_format($loanRequest->amount, 2) }}
                            @ {{ $loanRequest->interest_percentage}}%
                        </a>
                    </h4>
                    <p class="text-muted">
                        by {{$loanRequest->user->reference}} for
                        {{ $loanRequest->duration }} {{str_plural('month', $loanRequest->duration)}}
                    </p>
                    <p class="mt-4 card-text">
                        {{ $loanRequest->comment}}
                    </p>
                    
                    
                    @component('components.lr-status', ['loanRequest' => $loanRequest])
                    @endcomponent
                    <br/><br/>
                    
                    <div class="border-top pt-3">
                        <div class="row">
                            <div class="col-6">
                                <h6>Monthly Return</h6>
                                <p>
                                    {{$loanRequest->emi()}}
                                </p>
                            </div>
                            <div class="col-6">
                                <h6>Funds Responses</h6>
                                <p>
                                    {{ $loanRequest->funds()->count() }}
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

@endsection

@section('page-js')
  
@endsection