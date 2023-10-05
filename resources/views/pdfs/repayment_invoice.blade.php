<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Authority</title>
</head>
<body style="border-radius: 5px; margin:0; padding: 0; box-sizing:border-box; font-family: 'Century Gothic', sans-serif; background-image: url({{ asset('/logo_pack/images/invoice_bg.png') }}); background-position: 85% -3%; background-size: 800px 0px; background-repeat: no-repeat">
    <div style="width: 595px; box-sizing:border-box; padding-left: 10px;">
        <div style="margin-top: 65px;">
            <div style="color: #50565B; display:inline-block; font-size: 90%; width: 70%; padding-top: 20px;">
                <img src="{{ asset('/logo_pack/logo/colored/128.png') }}" style="height: 60px; "/>
                <p style="margin-top: -3px">No. 6 Abuja Lane,</p>
                <p style="margin-top: -10px">DLine, Port Harcourt,</p>
                <p style="margin-top: -10px">Rivers State, Nigeria</p>
            </div>
            <div style="display:inline-block;width: 60px; height: 60px; width: 70%; margin-left: 70px;">
                <h1 style="color:#2367A0; letter-spacing: 12px; margin-bottom: 5px; margin-top: 10px">INVOICE</h1>
                <div style="color: #50565B; font-size: 90%;">
                    <p style="margin-top: 3">No: &nbsp;<span style="font-weight: bold">{{ $invoiceNumber }}</span></p>
                    <p style="margin-top: -10px">Date: &nbsp;<span style="font-weight: bold">{{ now()->toDateString() }}</span></p>
                    <p style="margin-top: -10px">Payment Due: &nbsp;<span style="font-weight: bold">{{ $dueDate }}</span></p>
                </div>
            </div>
            <div style="border-top: 2.5px dashed #59E1DD; height: 1px; width: 120%; margin-top: -90px"></div>
        </div>
        
        <div style="margin-top: -70px; margin-bottom: 30px;">
            <p style="font-size: 80%; text-transform: uppercase; color: gray; margin-bottom: 0px;">CUSTOMER ID</p>
            <h3 style="color: #A61E22; margin-top: 2px; font-weight: bold">{{ $user->reference }}</h3>
        </div>
        <div>
            <div style="width: 120%;">
                <div style="font-weight: bold; padding: 14px; font-size: 14px; color: #50565B; background: #59E1DD;">
                    <p style="display: inline-block; width: 85%; margin: 0;">Unpaid EMI</p>
                    <p style="display: inline-block; width: 15%; margin: 0;">Amount (N)</p>
                </div>
                @foreach($paymentItems as $item)
                <div style="padding: 12px; font-size: 14px; color: #50565B; background: #F0F4F7; margin-top: 8px;">
                    <p style="display: inline-block; width: 85%; margin: 0;">Repayment for Month {{$item->month_no}}</p>
                    <p style="display: inline-block; width: 15%; margin: 0;">{{ number_format($item->emi + $item->management_fee, 2) }}</p>
                </div>
                @endforeach
                
                <div style="padding: 14px; font-size: 14px; color: #50565B; margin-top: 10px; font-weight: bold;">
                    <p style="display: inline-block; width: 82%; text-align: right; margin: 0; padding-right: 20px;">Total: </p>
                    <p style="display: inline-block; width: 18%; margin: 0;">N {{ number_format($totalAmount, 2)}}</p>
                </div>
            </div>
         </div>
         <div style="margin-top: 20px">
            <div style="border-top: 2.5px dashed #59E1DD; height: 1px; width: 120%;"></div>
            <p style="width: 120%; font-size: 95%; color: #A61E22; text-align: center">NOTE: EMI will switch to recovery if unpaid for the next 30 days with a minimum 10% penal charge</p>
         </div>
    </div>
</body>
</html>