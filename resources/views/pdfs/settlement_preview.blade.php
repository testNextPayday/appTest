<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Loan Settlement Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            border: 1px solid black;
            font-size: 87%;
        }

        .badge {
            color: white;
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-warning {
            background-color: #ffc107;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        table td {
            border: 1px solid black;
            padding: 5px;
            width: 100%;
            font-size: 80%;
        }
    </style>
</head>

<body style="margin:0; padding: 0;  font-family: 'Century Gothic', sans-serif">
    <main style="width: 98%;  margin:auto">


        <div>

            <div id="header" style="text-align:center">
                <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/logo_pack/logo/colored/64.png' }}" />
                <h1 style="text-align:center;border:1px solid black;letter-spacing:5px;"> Settlement Statement </h1>
            </div>

            <div>


                <div style="float:right">Customer Name : {{ $loan->user->name }}</div>
                <div>
                    Loan Reference : {{ $loan->reference }}
                </div>
                <br />

                <div>

                    <section style="">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Principal Amount</td>
                                    <td>{{ number_format($loan->amount, 2) }}</td>
                                    <td></td>
                                    <td>Loan Released Date</td>
                                    <td>{{ $loan->created_at }}</td>
                                </tr>
                                <tr>
                                    <td> EMI</td>
                                    <td>
                                        {{ $loan->repaymentPlans->first()->is_new ? number_format($loan->emi, 2) : number_format($loan->repaymentPlans->first()->emi + $loan->repaymentPlans->first()->management_fee, 2) }}

                                    </td>
                                    <td></td>
                                    <td>Payment Closed</td>
                                    <td>{{ $loan->closedPayments()->count() }}</td>
                                </tr>
                                <tr>
                                    <td>Loan Tenor</td>
                                    <td>{{ $loan->duration }}</td>
                                    <td></td>
                                    <td>Penalty</td>
                                    <td>
                                        @if ($loan->is_penalized)
                                            {{ 'Automated' }}
                                        @else
                                            @php($cur_month = $loan->created_at->diffInMonths(now()))
                                            @if ($cur_month <= 3)
                                                {{ 10 }}%
                                            @elseif($cur_month <= 6)
                                                {{ 7.5 }}%
                                            @elseif($cur_month <= 9)
                                                {{ 5 }}%
                                            @else
                                                {{ 0 }}%
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                    <section>
                                            <br>
                                            <h5>Loan Fulfilment</h5>

                                            <table class="table table-bordered">
                                                @php($loan_wallet = $loan->user->loan_wallet)
<?php
    $currentValueOfLoan = $loan->amount + $loan->accrued_amount + $loan_wallet;
    $valueOnClosure = $currentValueOfLoan - $loan->deductions;
    $totalDue =  $valueOnClosure + $loan->penal_charge;
?>







                                                <tbody>
                                                    <tr>
                                                        <td>Released Amount</td>
                                                        <td>{{number_format($loan->amount,2)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>  Interest and Fees to Date</td>
                                                        <td>{{number_format($loan->accrued_amount,2)}}</td>
                                                    </tr>
                                                    @if($loan_wallet > 0)
                                                        <tr>
                                                            <td> &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Late Repayment Charges</td>
                                                            <td>{{number_format($latePaymentCharges, 2)}}</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td>  Current Value of Loan</td>
                                                        <td>{{number_format($currentValueOfLoan, 2)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><span style="font-weight:bold;"></span>Less: Repayments</td>
                                                        <td>{{number_format($loan->deductions,2)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td> Loan Wallet Balance</td>
                                                        <td>{{number_format(abs($loan->user->loan_wallet),2)}}</td>
                                                    </tr>
                                                    
                                                    <!-- <tr>
                                                        <td> &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Repayment Wallet Balance</td>
                                                        <td>{{number_format($loan->repayment_wallet,2)}}</td>
                                                    </tr>
                                                -->
                                                    <!-- If the loan wallet is greater than 0 add it up -->
                                                                                                        <tr>
                                                        <td>  Value on Closure</td>
                                                        <td>{{number_format($valueOnClosure, 2)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <span style="font-weight:bold;"> Add: &nbsp;&nbsp;</span>
                                                            {{$loan->is_penalized ? 'Penal Charge' : 'Preliquidation Charge'}}
                                                        </td>
                                                        <td>{{number_format($loan->penal_charge,2)}}</td>
                                                    </tr>
                                                    <!-- If there is a negative balance and loan is not penalized in the wallet greater than 100 add to the settlement amount -->
                                                   
                                                    <tr>
                                                        
                                                        <td><b>  Total Due @ {{now()}}</b></td>
                                                        <td>{{number_format($totalDue, 2)}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                    </section>
                    <div>
                        <p><b>INSTRUCTIONS:</b> You can pay online above or use the bank details below to pay in the
                            bank <br>
                            Payment should be made within 24hrs after viewing
                            this settlement report since charges are accrued daily <br>
                            There will be no refund after settlement.
                            </p>
                        <table class="table table-bordered" style="width:70%">
                            <tbody>
                                <tr>
                            <td>Bank Name:</td>
                            <td>Stanbic IBTC Bank</td>
                        </tr>
                        <tr>
                            <td>Account Number</td>
                            <td>0051208788</td>
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


                <div>

                </div>


            </div>
 
        </div>
        <!-- /.conainer-fluid -->

    </main>
</body>

</html>
