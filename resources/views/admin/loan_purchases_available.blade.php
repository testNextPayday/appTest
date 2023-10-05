@extends('layouts.admin')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Loans available for sale</li>
    </ol>
    <div class="container-fluid" style="min-height:80vh">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-sm-12">
                    <div class="callout card m-0 py-2 text-muted text-center bg-light text-uppercase">
                        <small><b>Loans available for purchase</b></small>
                    </div>
                </div>
                @if(count($loans))
                @foreach($loans as $loan)
                <?php 
                    $loaneeLoan = $loan->loanRequest->loan;
                    $totalCollectedByLoanee = $loaneeLoan->amount;
                    $fundFraction = $loan->amount/$totalCollectedByLoanee;
                    $currentValue = $fundFraction * ($loan->loanRequest->loan->repaymentPlans()->whereStatus(false)->sum('interest') + 
                                                    $loan->loanRequest->loan->repaymentPlans()->whereStatus(false)->sum('principal')); 
                ?>
                <div class="col-lg-6">
                    <div class="card card-hover">
                        <div class="card-body" style="height:200px; padding: 20px;">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h4 class="card-title text-behance">{{$loan->reference}}</h4>
                                    <div class="h3 text-primary mb-2 mt-2"><i class="icon-direction"></i>&nbsp; 
                                        ₦ {{number_format($loan->sale_amount, 2)}}
                                    </div>
                                    <div class="h4 text-danger mb-2 mt-2"> 
                                        Current Value: 
                                        <strong>
                                            ₦ {{
                                                number_format(
                                                    $loan->currentValue
                                                , 2)
                                            }}
                                        </strong>
                                    </div>
                                    <p>
                                        Unearned Interest:<strong> ₦ 
                                        {{
                                            $loan->potential_gain
                                        }}
                                        </strong>
                                    </p>
                                    <p>Loan Due Date:<strong>
                                         {{$loan->loanRequest->loan->due_date}}
                                    </strong></p>
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
                                {{$loans->links()}}  
                            </div>
                        </div>
                    </div>
                
                @else
                <div class="col-sm-12 p-3 text-center">There are no loans available for purchase</div>
                @endif
          </div>
          <!--/.row-->
          
        </div>
    </div>
</main>
@endsection

@section('page-js')
  
@endsection