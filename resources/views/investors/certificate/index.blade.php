@extends('layouts.investor')
@section('page-css')
<style id="page-style">
    #container {
        width: 70%;
        margin: auto;
    }

    #cert {
        font-family:'Blackadder ITC Regular';
    }
    .each {
        display: grid;
        grid-template-columns: 1fr 1fr;
        font-size: 1.4rem;
        margin-bottom: 10px;
        color: grey;
        text-align: left;
        font-family: "Centaur Regular";
    }

    #cert-holder {
        width: 90%;
        margin: auto;
        padding: 15px;
        border-width: 15px;
        border-style: groove;
        border-image: url('../images/border.png') 30 round stretch;


    }

    #cert-owner {
        color: #ee472c;
        margin: 20px;
        font-family: "Bradley Hand ITC Regular";

    }

    @media print {
        .no-print{
            display: none;
        }

        nav {
            display: none;
        }

    }

   
</style>
@endsection
@section('content')

<div class="content-wrapper">
    <div class="row mb-4 no-print">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title" style="color:grey;">My Certificate</h4>
            <a class="btn btn-success"  target="_blank" href="{{route('investors.certificate.pdf',['reference'=>$investor->reference])}}"  style="float:right"><i class="fa fa-print"></i>(PDF)</a>
        </div>
    </div>

    @php($loanFundAmount = $investor->loanFunds->sum('amount'))
    <div class="row">
        <div class="col-md-12">
            <div class="card support-pane-card">
                <div class="card-body">
                    <div id="cert-holder">
                        <div style="">
                            <span style="float:right;color:grey">Certificate No: {{$investor->certificate_number}}</span>
                            <img src="{{asset('logo_pack/logo/colored/64.png')}}">
                            <h1 id="cert" style="color:gray;padding-top:30px;padding-bottom:20px;font-weight:300;font-size:3.8rem;text-align:center ">Investment Certificate</h1>
                            
                        </div>
                        <div style="padding-top: 20px;">
                            <div id="container">
                                <div style="text-align:center;color:grey">This is to certify that</div>
                                <div style="text-align:center">
                                    <h2 id="cert-owner">{{$investor->name}}</h2>
                                    <hr>
                                </div>
                                <div style="text-align:center;color:grey">
                                    <p>Is an investor with Nextpayday and the details of investment is stated as follows:</p>
                                </div>

                                <div class="each">
                                    <div>Present Value :</div>
                                    <div>₦ {{number_format($investor->presentValue())}}</div>
                                </div>
                                

                                <div class="each">
                                    <div>No. of Withdrawals : </div>
                                    <div> {{$investor->getSuccessfulWithdrawals()->count()}}</div>
                                </div>

                                <div class="each">
                                    <div>No. of Loans Backed : </div>
                                    <div>{{$investor->getBackedLoans()}}</div>
                                </div>

                                <div class="each">
                                    <div>Portfolio Size: </div>
                                    <div>₦ {{ number_format(optional($investor)->portfolioSize(), 2)}}</div>
                                </div>

                                <div class="each">
                                    <div>Wallet Balance: </div>
                                    <div>₦ {{number_format(optional($investor)->wallet, 2)}}</div>
                                </div>

                                <div>
                                    <div style="float:right">
                                        <img src="{{asset('logo_pack/docs/signature.png')}}" height="90px" />
                                        <p>Authorized Signature</p>
                                    </div>

                                    <div>
                                        <img src="{{asset('logo_pack/docs/godswill_signature.png')}}" height="90px" />
                                        <p>Authorized Signature</p>
                                    </div>
                                    
                                    
                                </div>
                                <div style="text-align:center">
                                    <span>Date : {{date("l jS \of F Y ")}}</span>
                                </div>
                                <br>
                                <div style="text-align:center">
                                    <img src="{{asset('logo_pack/docs/investment_assurance2.png')}}" height="90px" />

                                </div>


                            </div>


                        </div>
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

<script>


@endsection