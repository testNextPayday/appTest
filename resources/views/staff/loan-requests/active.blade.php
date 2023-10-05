@extends('layouts.staff-new')

@section('page-css')
    <style>
        .checked {
            color: orange;
        }
    </style>
@endsection

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Loan Requests</li>
    </ol>
    <div class="container-fluid" style="min-height:80vh">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12">
                    <div class="callout card m-0 py-2 text-muted text-center bg-light text-uppercase">
                        <small><b>Available Loan Requests</b></small>
                    </div>
                </div>
                @if(count($loanRequests))
                @foreach($loanRequests as $loanRequest)
                <div class="col-lg-6">
                    <div class="card card-hover">
                        <div class="card-body" style="min-height:300px; padding: 20px;">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <span class="bid-component">
                                        <staff-fund-loan
                                            key="{{$loanRequest->id}}"
                                            :loan-request="{{$loanRequest}}"
                                            :funders="{{$loanRequest->funders()}}"
                                            :accounts="{{$staff->investors}}"></staff-fund-loan>
                                    </span>
                                    
                                    <div class="h3 text-primary mb-2 mt-2"><i class="icon-paper-clip"></i>&nbsp; 
                                        ₦ {{number_format($loanRequest->amount, 2)}} @ {{$loanRequest->interest_percentage}} %
                                    </div>
                                    <hr/>
                                    <p>
                                        Loan Duration: <strong>{{$loanRequest->duration}} Months
                                    </strong></p>
                                    <p>
                                        Borrower : {{$loanRequest->user->name}}
                                    </p>
                                    <p>
                                        Monthly Return: <strong>₦ {{number_format($loanRequest->monthlyPayment($loanRequest->amount),2)}}
                                    </strong></p>
                                    
                                    <p>
                                        <a class="label label-primary" 
                                            data-toggle="collapse" 
                                            href="#collapse-{{$loanRequest->id}}" role="button" aria-expanded="false" 
                                            aria-controls="collapse-{{$loanRequest->id}}">
                                            Borrower Employment Data
                                        </a>
                                    </p>
                                    
                                    <div class="collapse" id="collapse-{{$loanRequest->id}}">
                                        <div class="card card-body">
                                            <?php 
                                                $employment = $loanRequest->employment 
                                            ?>
                                            @if($employment)
                                            <p>Employer: <strong>{{$employment->employer->name}}</strong></p>
                                            <p>Position: <strong>{{$employment->position}}</strong></p>
                                            <p>Department: <strong>{{$employment->department}}</strong></p>
                                            <p>State: <strong>{{$employment->employer->state}}</strong></p>
                                            @endif
                                        </div>
                                    </div>
                                    <hr/>
                                    <p class="text-muted"><small>{{$loanRequest->comment}}</small></p>
                                    <hr/>
                                    Risk Analysis
                                    @for($i=1; $i<=5; $i++)
                                        @if($loanRequest->risk_rating >= $i)
                                            <span class="fa fa-star checked"></span>
                                        @else
                                            <span class="fa fa-star"></span>    
                                        @endif
                                    @endfor
                                    
                                    <p class="text-muted" style="position:relative;bottom:0"><small><em>
                                        Posted on {{$loanRequest->created_at->toDateString()}}</em></small></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right" style="padding: 1px;">
                        </div>
                    </div>
                </div>
                <!--/.col-->
                @endforeach
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-footer">
                                {{$loanRequests->links()}}  
                            </div>
                        </div>
                    </div>
                
                @else
                <div class="col-sm-12 p-3 text-center">There are no loan requests available</div>
                @endif
          </div>
          <!--/.row-->
          
        </div>
    </div>
</main>
@endsection

@section('page-js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js">

</script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
<script type="text/javascript" src="{{asset('assets/js/custom.js')}}"></script>
@endsection