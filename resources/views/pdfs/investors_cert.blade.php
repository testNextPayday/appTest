<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Investors Certificate</title>
  
    <style>
       

        #container {
            width: 95%;
            margin: auto;
            border-width: 10px;
            border-style: inset;
            border-color: grey;
            padding:20px;
        }

        #cert_head {
            color: gray;
            padding-top: 10px;
            padding-bottom: 10px;
            font-weight: 300;
            font-size: 3.5rem;
         
            text-align: center;
            font-style: italic;
        }

        .each {

            font-size: 17px;
            margin-bottom: 10px;
            color: grey;
            text-align: left;
            font-family: "Centaur Regular";
            
        }

        .each div:first-child {
            float: right;
        }

        #cert-holder {
            width: 90%;
            margin: auto;
            padding: 40px;
            border-width: 10px;
            border-style: inset;
            border-color: grey;
            height : 100%;

        }


        #cert-owner {
            color: #ee472c;
            margin: 20px;
        }
    </style>
</head>

<body>
    <div id="container">

        <div id="cert-title-bar" style="margin-top:15px;">
            <span style="float:right;color:grey">Certificate No: {{$cert_number}}</span>
            <img src="{{$_SERVER['DOCUMENT_ROOT'].'/logo_pack/logo/colored/64.png'}}">
        </div>

        <div id="cert-header" style="margin-top:20px;">
            <h1 id="cert_head">Investment Certificate</h1>
        </div>

        <div id="cert-body" style="width:70%;margin:auto;">

            <div style="text-align:center;color:grey">This is to certify that</div>

            <div style="text-align:center">
                <h2 id="cert-owner">{{$investor->name}}</h2>
                <hr>
            </div>

            <div style="text-align:center;color:grey">
                <p>Is an investor with Nextpayday and the details of investment is stated as follows:</p>
            </div>

            <div class="list-holder" style="margin-bottom:50px;margin-top:50px;">
                <div class="each">
                    <div>NGN {{number_format($presentValue,2)}}</div>
                    <div>Present Value : </div>

                </div>

                <div class="each">
                    <div> {{$withdrawals}}</div>
                    <div>No. of Withdrawals : </div>

                </div>

                <div class="each">
                    <div>{{$backedLoans}}</div>
                    <div>No. of Loans Backed : </div>

                </div>

                <div class="each">
                    <div>NGN {{ number_format($portfolioSize, 2)}}</div>
                    <div>Portfolio Size: </div>

                </div>

                <div class="each">
                    <div>NGN {{number_format(optional($investor)->wallet, 2)}}</div>
                    <div>Wallet Balance: </div>

                </div>
            </div>

            <div id="signature-holder" style="margin-top:20px;">

                <div id="signature1" class="signature" style="float:right">
                    <img src="{{$_SERVER['DOCUMENT_ROOT'].'/logo_pack/docs/signature.png' }}" height="90px" />
                    <p>Authorized Signature</p>
                </div>

                <div id="signature1" class="signature">
                    <img src="{{$_SERVER['DOCUMENT_ROOT'].'/logo_pack/docs/godswill_signature.png' }}" height="90px" />
                    <p>Authorized Signature</p>
                </div>
            </div>

            <div id="date-holder" style="text-align:center;margin-top:20px; margin-bottom:20px;">
               
                <span>Date : {{date("l jS \of F Y ")}}</span>
                
            </div>


            <div id="assurance" style="margin-top:20px;margin-bottom:20px;">
                <div style="text-align:center">
                    <img src="{{$_SERVER['DOCUMENT_ROOT'].'/logo_pack/docs/investment_assurance2.png' }}" height="90px" />

                </div>
            </div>

        </div>
    </div>
    
</body>

</html>