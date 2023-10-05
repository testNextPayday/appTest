<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Loan Fulfillment Doc</title>
    <style>
    
        @font-face {
            font-family: 'Grenze Gotisch';
            src: url('/fonts/custom/GrenzeGotisch-Regular.tff') format('truetype');
            font-weight: 300;
            display : swap;
        }
    </style>
</head>

<body style="margin:0; padding: 0;  font-family: 'Grenze Gotisch', cursive;">
    <main style="width: 98%;  margin:auto">


        <div>

            <div id="header" style="text-align:center;margin-top:50px;">
                <img src="{{ $_SERVER['DOCUMENT_ROOT'].'/logo_pack/logo/colored/64.png' }}"  />
                <h1 style="text-align:center;border:1px solid black;letter-spacing:5px;margin-top:50px;"> Loan Completion Document </h1>
            </div>

            <div style="margin:20px; font-size:20px; font-style:monospace; line-height:1.5em; margin-top:40px;">

               <p style="">This document has been given to <b>{{$loan->user->name}}</b> to show that the loan with the following details</p>

               <div style="width:70%; margin:auto; line-height:2.5rem">
                    <div><b>Loan Reference : </b> {{$loan->reference}}</div>
                    <div><b>Amount : </b> NGN {{number_format($loan->amount, 2)}}</div>
                    <div><b>Duration : </b> {{$loan->duration}} month(s)</div>
               </div>

               <p>Has been completed by the borrower and fully paid for as at the date of issuance of this document being the {{now()->toDateString()}}</p>
                
            </div>

            <div style="margin-top:100px;width:50%;margin-left:auto;margin-right:auto;">
                <img src="{{ $_SERVER['DOCUMENT_ROOT'].'/logo_pack/docs/authorized_signature.png' }}"  height="120px" />
                <p style="margin-top:0px;">Authorized Signature</p>
            </div>

        </div>
        <!-- /.conainer-fluid -->

    </main>
</body>

</html>