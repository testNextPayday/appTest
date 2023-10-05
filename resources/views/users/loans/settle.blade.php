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

        <div id="header" style="text-align:center">
            <img src="{{asset('logo_pack/logo/colored/128.png')}}">
        </div>

        <div id="settle-page-body" style="width:80%;margin:30px 50px;">
            <div>
            
            
            </div>
            @component('components.settlement-chart',['loan'=>$loan])
            @endcomponent
           
            <div class="modal-body">
            
                <form method="POST" action="{{route('users.pay.settlement',['reference'=>$loan->reference])}}" >
                    {{csrf_field()}}
                    <input type="hidden" name="amount" value="{{$loan->settlement_total * 100}}">
                    <input type="hidden" name="email" value="{{$loan->user->email}}">
                    <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                    <input type="hidden" name="metadata" value="{{ json_encode($array = ['loan_reference' => $loan->reference,]) }}" > 

                    <button type="submit" class="btn btn-primary">Pay Online (charge ₦{{number_format(paystack_charge($loan->settlement_total))}})</button>
                   
                </form>
            </div>

            <div>
                <p><b>INSTRUCTIONS:</b> You can pay online above or use the bank details below to pay in the bank <br>
                and upload <span style="color:red">proof of payment i.e teller or screenshot in less than 24hrs</span> after viewing<br>
                this settlement report since charges are accrued daily</p>
                <table class="table table-bordered" style="width:60%">
                    <tbody>
                        <tr>
                            <td>Bank Name:</td>
                            <td>Wema Bank</td>
                        </tr>
                        <tr>
                            <td>Account Number</td>
                            <td>0123666554</td>
                        </tr>
                        <tr>
                            <td>Account Name</td>
                            <td>Nextpayday</td>
                        </tr>
                        <tr>
                            <td>Purpose</td>
                            <td>Pre-liquidation of Nextpayday Loan</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            
        </div>

    </div>
    <!-- /.conainer-fluid -->

</main>
@endsection

@section('page-js')
@endsection