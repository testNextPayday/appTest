<!DOCTYPE html>
<html style="width:100%;height:100%;padding:0px;margin:0px;">

<head>
    <title>Hilcop Certificate</title>
    
    <style>
    
        @font-face {
            font-family: 'Grenze Gotisch';
            src: url('/fonts/custom/GrenzeGotisch-Regular.tff') format('truetype');
            font-weight: 300;
            display : swap;
        }
    </style>
</head>

<body style="width:1100px;height:750px;margin:auto;margin-top:20px;padding:0px;background-image: url({{asset('logo_pack/docs/hilltop_cert2.png')}}); background-repeat: no-repeat;
    background-size:100% 100%;">

    <div style="width:80%;margin:auto;padding:0;font-family: 'Grenze Gotisch', cursive;font-size:21px;">
        <div style="padding:15px;background-position:top center;width:100%;">
            <div style="text-align: center;padding-top: 180px;width:100%;margin-bottom:20px;margin-top:50px;">

                <div style="width:100%">
                    <div style="float:right;font-weight:bold;"> Number of Shares : {{$no_of_shares}}</div>
                    <div style="text-align:left;font-weight:bold;">Certificate No: {{$reference}}</div>
                </div>
                <div style="width:100%;margin-top:10px;line-height:30px;">
                    <p style="padding-top:4px; padding-bottom:2px">This is to certify that <span style="font-weight:bold;font-size:198%;margin-left:50px;margin-right:50px;">{{strtoupper($name)}}</span> of {{$address}}</p>

                    <p style="padding:2px;">was at <span style="font-weight:bold">{{date('d/m/Y',strtotime($membership_date))}}</span> the registered holder of <span style="font-weight:bold">{{$no_of_shares}}</span> Ordinary shares of <span style="font-weight:bold">NGN{{number_format($value_per_share,2)}}</span> each fully paid in  <span style="font-weight:bold">Port Harcourt Hilltop Investment and Credit Cooperative Society</span> and subject to the Articles of Association of the Co-operative Society</p>
                </div>


            </div>

            <div style="width:100%;margin-top:50px;">
                <span style="float:right">
                    <img src="{{asset('logo_pack/docs/secretary.png')}}" height="90px" />
                    <p style="margin-top:0px;">Signed/Secretary</p>
                </span>

                <span>
                    <img src="{{asset('logo_pack/docs/president.png')}}" height="90px" />
                    <p style="margin-top:0px;">Signed/President</p>
                </span>
            </div>


        </div>
    </div>

</body>

</html>