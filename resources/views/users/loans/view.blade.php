@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('users.loans.received')}}">Loans</a></li>
        <li class="breadcrumb-item active">{{$loan->reference}}</li>

        <li class="breadcrumb-menu">
          <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-primary btn-lg waves-effect text-white" href="{{route('users.loan-requests.create')}}" style="border-radius: 20px;"> 
             <span style="font-size: 0.9rem;"> <i class="icon-cursor text-white"></i> Get Loan</span>
            </a>
          </div>
        </li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            @if ($errors->any())
            <div class="row justify-content-center">
                <div class="col-sm-6">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>
                                Loan Details
                                <span class="pull-right">
                                    @if($loan->is_penalized)
                                        <span class="badge badge-danger">
                                            <i class="fa fa-exclamation-triangle"></i>
                                            Penalised
                                        </span>
                                    @endif
                                    @component('components.loan-status', ['loan' => $loan])
                                    @endcomponent
                                    @component('components.loan-statement-button',['loan'=>$loan])
                                    @endcomponent
                                </span>
                            </h4>

                        </div>
                        <div class="card-body">
                            <div class="card-group mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-diamond"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($loan->amount, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Loaned Amount</small>
                                        <?php
                                        $insurance = 2.5 / 100 * $loan->amount;
                                        ?>
                                        <small class="text-muted text-uppercase font-weight-bold">INSURANCE:
                                            ₦{{number_format($insurance, 2)}}</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-note"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loan->due_date->diffInMonths($loan->created_at)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Loan Tenure
                                            (Months)</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-compass"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loan->created_at}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Loan Date</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-speedometer"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loan->due_date}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Due Date</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Loan Documents</h4>
                            <p class="text-secondary">
                                Documents you upload will show up here
                            </p>
                        </div>
                        <div class="card-body">
                            @php($documents = json_decode($loan->collection_documents,true) ?? [])

                            @if (!count($documents) > 0)
                            <button class="btn btn-sm btn-secondary" disabled>
                                No documents available
                            </button>
                            @endif

                            @foreach($documents as $name => $document)
                            <a href="{{ asset(Storage::url($document))}}" target="_blank"
                                class="btn btn-primary btn-sm">
                                {{ ucwords(str_replace("_", " ", $name)) }}
                            </a>
                            @endforeach

                            @if($loan->status == 2)
                                <a class="btn btn-primary btn-sm" href="{{route('view.loan.fulfillment-doc',['reference'=>$loan->reference])}}" target="_blank">Loan Fulfillment Doc</a>   
                            @endif
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>

            @if( $loan->canSettle())
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Loan Settlement</h4>
                            <p class="text-secondary">
                                Take settlement action here
                            </p>
                        </div>
                        <div class="card-body">
                            @if(isset($loan->settlement) && $loan->settlement->status == 1)
                            <button class="btn btn-primary" disabled> Processing</button>
                            <a href="{{route('settlement.invoice.view',['reference'=>$loan->settlement->reference])}}"
                                target="_blank" class="btn btn-primary btn-sm">
                                View document
                            </a>
                            @elseif(isset($loan->settlement) && $loan->settlement->status == 3)
                            <button class="btn btn-danger" disabled> Declined</button>
                            <a href="{{route('settlement.invoice.view',['reference'=>$loan->settlement->reference])}}"
                                target="_blank" class="btn btn-primary btn-sm">
                                View document
                            </a>
                            Reason: {{$loan->settlement->status_message}}
                            @elseif(isset($loan->settlement) && $loan->settlement->status == 2)
                            <button class="btn btn-success"> Your settlement has been Confirmed</button><br>
                            <a href="{{route('settlement.invoice.view',['reference'=>$loan->settlement->reference])}}"
                                target="_blank" class="btn btn-primary btn-sm">
                                View document
                            </a>
                            @else
                            <a href="{{route('users.apply.settlement',['reference'=>$loan->reference])}}"><button
                                    class="btn btn-sm btn-primary">View Settlement Details </button></a>
                            @endif
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Management Panel</h4>
                            <p class="text-secondary">
                                Actions you can take through the processing period of this loan will show up here
                            </p>
                        </div>
                        <div class="card-body">
                            @component('components.loan-actions-customer', ['loan' => $loan])
                            @endcomponent
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            
            @if($loan->is_penalized)
                @component('components.penalty_schedule_others', ['loan'=> $loan])
                @endcomponent
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Repayment Plan
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($loan->repaymentPlans->isNotEmpty() && $loan->repaymentPlans->first()->is_new )
                                    <div class="col-12 table-responsive">
                                        <caption>EMI: {{number_format($loan->repaymentPlans->first()->emi,2)}}</caption>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>

                                                    <th>Total Amount</th>
                                                    <th>
                                                        Collected Date
                                                    </th>
                                                    <th>Amount Paid</th>
                                                    <th>Due Date</th>
                                                    <th>Payment Proof</th>
                                                    <th>Payment Method</th>
                                                    <th>Status</th>

                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($i = 1)
                                                @foreach ($loan->repaymentPlans as $plan)
                                                <tr>
                                                    <td>{{$i}}</td>

                                                    <td>{{number_format($plan->totalAmount,2)}}</td>



                                                    <td>{{$plan->date_paid}}</td>
                                                    <td>@if($plan->status == 1)
                                                        {{number_format($plan->paid_amount,2)}}
                                                        @endif
                                                    </td>
                                                    <td>{{$plan->payday}}</td>
                                                    @if($plan->payment_proof)
                                                    <td><a target="_blank"
                                                            href="{{Storage::url($plan->payment_proof)}}">View</a></td>
                                                    @else
                                                    <td><a target="_blank" href="{{$plan->payment_proof}}">No proof</a></td>
                                                    @endif
                                                    <td>{{$plan->collection_mode}}</td>
                                                    <td>
                                                        @if($plan->status)
                                                        <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                                                        @else
                                                        <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                                                        @endif
                                                    </td>




                                                </tr>
                                                @php(++$i)
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else

                                    <div class="col-12 table-responsive">
                                        <table id="" class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>

                                                    <th>Total Amount</th>
                                                    <th>Due Date</th>
                                                    <th>Balance</th>
                                                    <th>
                                                        Collected Date
                                                    </th>
                                                    <th>Payment Proof</th>
                                                    <th>Payment Method</th>
                                                    <th>Status</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($loan->repaymentPlans as $plan)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>

                                                    <td>₦{{ number_format($plan->totalAmount, 2)}}</td>
                                                    <td>{{$plan->payday}}</td>

                                                    <td>
                                                        @if($loop->iteration === $loan->loanRequest->duration)
                                                        ₦0.00
                                                        @else
                                                        ₦{{ number_format($plan->balance, 2) }}
                                                        @endif
                                                    </td>
                                                    <td>{{$plan->date_paid}}</td>
                                                    @if($plan->payment_proof)
                                                    <td><a target="_blank"
                                                            href="{{Storage::url($plan->payment_proof)}}">View</a></td>
                                                    @else
                                                    <td><a target="_blank" href="{{$plan->payment_proof}}">No proof</a></td>
                                                    @endif
                                                    <td>{{$plan->collection_mode}}</td>
                                                    <td>
                                                        @if($plan->status)
                                                        <a class="btn btn-xs btn-block btn-success" href="#">Paid</a>
                                                        @else
                                                        <a class="btn btn-xs btn-block btn-warning" href="#">Not Paid</a>
                                                        @endif
                                                    </td>


                                                </tr>

                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">Repayments not found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>


                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/.col-->
                </div>
            @endif

            
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->


    <div id="topUp" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Top up loan</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{route('users.loan-requests.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <?php 
                                    $loanApplicationFee = App\Models\Settings::loanRequestFee();
                                    $pay_charge = $loanApplicationFee + paystack_charge($loanApplicationFee);
                                    $employment = Auth::user()->employments()->with('employer')->get()->last();
                                    
                                    $referrer = optional();

                                    if (!Auth::user()->adder_type == 'AppModelsInvestor') {
                                        $referrer = Auth::user()->referrer;
                                    }

                                    // Get the last incomplete requests
                                    $incompleteRequest = Auth::user()->incompleteRequests->last();
                                ?>
                            
                            @if(!$employment->employer || !$employment->employer->upfront_interest)                
                                <form method="POST" onmousemove="formButton()" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <loan-request-form-section 
                                        :employments="{{Auth::user()->employments()->with('employer')->get()}}" :loan_fee="{{$loanApplicationFee}}" 
                                        :application_fee="{{$pay_charge}}" :users="{{json_encode($users)}}" :user="{{Auth::user()}}" :payurl="'{{config('paystack.publicKey')}}'" :monokey="'{{config('monostatement.clientPublic')}}'" :affiliatecode="'{{optional($referrer)->reference}}'"
                                        :request="{{json_encode($incompleteRequest)}}">
                                    </loan-request-form-section>
                                    <!-- <user-referral :code="'{{optional($referrer)->reference}}'" :users="{{json_encode($users)}}"></user-referral> -->
                                </form>
                            @else
                                <form method="POST" onmousemove="formButton()" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <salary-now-loan-request-form-section 
                                        :employments="{{Auth::user()->employments()->with('employer')->get()}}" :loan_fee="{{$loanApplicationFee}}" 
                                        :application_fee="{{$pay_charge}}" :users="{{json_encode($users)}}" :user="{{Auth::user()}}" :payurl="'{{config('paystack.publicKey')}}'" :monokey="'{{config('monostatement.clientPublic')}}'" :affiliatecode="'{{optional($referrer)->reference}}'"
                                        :request="{{json_encode($incompleteRequest)}}">
                                    </salary-now-loan-request-form-section>
                                    <!-- <user-referral :code="'{{optional($referrer)->reference}}'" :users="{{json_encode($users)}}"></user-referral> -->
                                </form>
                            @endif
                                
                            </div>           

                            <!-- <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-dot-circle-o"></i> Submit</button> -->
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

</main>
@endsection
@section('page-js')

<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> 
<script type="application/javascript" src="https://connect.withmono.com/connect.js"></script>

<script type="text/javascript">
  function formButton() {
    var bank    = $('#bank_statement').val();
    var amount  = $('#amount').val();
    var comment = $('#textarea-input').val();
    if (amount == '' || bank == '' || comment == '') {
        $("#booking-form").attr('disabled',true);
      }else{
         $("#booking-form").attr('disabled',false);
      }
  }
</script>

@endsection