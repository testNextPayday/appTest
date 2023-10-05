<!DOCTYPE html>
<html>
<head>
	<title>Certificate</title>
</head>
<body style="border: 1px dashed #555; height:842px;width:602px;margin:0;padding:0;margin-left:60px;">

	<div style="background-image: url({{asset('logo_pack/docs/investment_cert_bg.png')}}); height:842px;width:602px;background-repeat: no-repeat;
    background-size: 602px 842px;">
		<div style="text-align: center;padding-top: 220px;">
			<p style="padding-top:4px; padding-bottom:2px">This promissory note certifies that</p>
			<h3><span style="color:blue;">{{$name}}</span></h3>
			<p style="padding:2px;">Invested <span style="color:blue;">NGN {{number_format($amount, 2)}} ({{ucwords($amount_in_words)}} Naira Only) @ {{$rate}}% per annum</span></p>
			<p style="padding:2px;">on <span style="color:blue;">{{$start_date->format('l jS \\of F Y')}}</span></p>
			<p style="padding:2px;">Interest Payment Cycle: <span style="color:blue;">{{$interest_payment_cycle}}</span></p>
			<p style="padding:2px;">Certificate Number: <span style="color:blue;">{{$reference}}</span></p>
			<!-- <p style="padding:2px;">Maturity Value: <span style="color:blue;">NGN {{number_format($maturity_value, 2)}}</span>.</p>
			<p style="padding:2px;">Tax Value: <span style="color:blue;">NGN {{number_format($tax_amount, 2)}}</span>.</p> -->
			@if (strtolower($interest_payment_cycle) == 'monthly')
			<p style="padding:2px;">Monthly Interest : <span style="color:blue;"> NGN {{number_format($monthly_interest, 2)}}</span></p>
			@endif
			
			<p style="padding:2px;">Maturity Value: <span style="color:blue;">NGN {{number_format($maturity_value, 2)}}</span>.</p>
			<p style="padding:2px;">Maturity Date: <span style="color:blue;">{{$maturity_date->modify('-1 months')->format('l jS \\of F Y')}}</span>.</p>
			<img src="{{asset('logo_pack/docs/signature.png')}}" height="90px" />
			<p>Signed/Sealed</p>
			<div style="width:100%;height:20px"></div>
		</div>
	</div>

</body>
</html>