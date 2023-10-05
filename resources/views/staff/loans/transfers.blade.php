@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Loan Transfers</li>
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
                @php($loan->potential_gain = $loan->potential_gain)
                <div class="col-lg-6">
                    <div class="card card-hover">
                        <div class="card-body" style="height:200px; padding: 20px;">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <span class="bid-component">
                                        <staff-loan-bid-component :loan="{{$loan}}"
                                            :loan-value="{{$loan->currentValue}}"
                                            :bidders="{{$loan->bidders}}"
                                            :accounts="{{Auth::guard('staff')->user()->investors}}">
                                        </staff-loan-bid-component>
                                    </span>
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
                                        Gain:<strong> ₦ 
                                        {{
                                           
                                                $loan->potentialGain
                                        
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