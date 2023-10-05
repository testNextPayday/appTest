<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Authority</title>
</head>
<body style="margin:0; padding: 0; box-sizing:border-box; font-family: 'Century Gothic', sans-serif">
    <div style="width: 595px; box-sizing:border-box; padding-left: 10px;">
        <div>
            <h1 style="color: grey; text-transform: uppercase; font-size: 115%; display:inline-block; width: 130%; padding-top: 20px;">
                Investment Statement
            </h1>
            <div style="display:inline-block;width: 60px; height: 60px; width: 10%; margin-left: 70px; text-align: right;">
                <img src="{{ asset('/logo_pack/icon/colored/icon_colored_128.png') }}" style="width:60px; height: 60px; "/>
            </div>
        </div>
        <div>
            <table border="0" style="width: 120%; border-collapse: collapse; border: 0">
                <tr style="color: black;">
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 10%">EMPLOYEE NAME</th>
                    <td style="font-size: 12px; border: 1px solid gray;padding: 8px; width: 15%" colspan="3">{{ strtoupper($name) }}</th>
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 10%">START DATE</th>
                    <td style="font-size: 12px; border: 1px solid gray;padding: 8px; width: 15%;">{{ $startDate }}</th>
                </tr>
                <tr style="color: black;">
                    <td style="font-size: 11px; font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 10%">PAYROLL NAME</th>
                    <td style="font-size: 12px; border: 1px solid gray;padding: 8px; width: 15%">{{ strtoupper($type) }}</th>
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 10%">PAYROLL NUMBER</th>
                    <td style="font-size: 12px; border: 1px solid gray;padding: 8px; width: 15%">{{ $payroll_id }}</th>
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 10%">END DATE</th>
                    <td style="font-size: 12px; border: 1px solid gray;padding: 8px; width: 15%;">{{ $endDate }}</th>
                </tr>
                <tr style="color: black;">
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 10%">POSITION TITLE</th>
                    <td style="font-size: 12px; border: 1px solid gray;padding: 8px; width: 15%" colspan="3">{{ strtoupper($position) }}</th>
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 10%">MDA</th>
                    <td style="font-size: 12px; border: 1px solid gray;padding: 8px; width: 15%;">{{ $employerName }}</th>
                </tr>
            </table>
            
         </div>

        <div>
            <br/>
            <p style="text-align: center; color: gray; text-transform: uppercase; font-weight: bold; width: 120%;">
                Deduction Description
            </p>
            <br/>
            
        </div>

        <div>
            <br/>
            <p style="text-align: center; color: gray; text-transform: uppercase; font-weight: bold; width: 120%;">
                Additional Info
            </p>
            <p style="border: 1px solid gray; padding: 1em; width: 115%; font-size: 90%; line-height: 28px; box-sizing: border-box;">
                This authority is issued in respect of the
                N {{ number_format($amount, 2)}} [ {{ ucwords($amountInWords) }} Naira] 
                nextpayday loan obtained from Unicredit ltd
            </p>

            <br/>
            <p style="text-align: center; color: gray; text-transform: uppercase; font-weight: bold; width: 120%;">
                Authorization
            </p>
            <p style="color: gray; font-size: 85%; width: 120%; line-height: 28px;">
                I UNDERSTAND THAT THIS FORM AUTHORIZES THE REDUCTION OF GROSS PAY
                BY THE AMOUNT OF DEDUCTIONS INDICATED ABOVE.  MY EMPLOYER IS AUTHORIZED
                TO DEDUCT A DIFFERENT AMOUNT SHOULD THERE BE A DEDUCTION CHANGE IN THE COURSE
                OF RECOVERY THROUGH PENALTIES, FINES, RESTRCURING OF THE FACILITY. 
                THIS DOCUMENT FURTHER AUTHORIZES UNICREDIT OR ANY OTHER NOMINATED COLLECTION AGENT
                TO MAKE PAYROLL DEDUCTIONS ON BEHALF OF UNICREDIT LTD.  
            </p>
        </div>
        <br/>
        <div>
            <table border="0" style="width: 120%; border-collapse: collapse; border: 0">
                <tr style="color: black;">
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 12%">EMPLOYEE SIGNATURE</th>
                    <td style="border: 1px solid gray;padding: 8px; width: 20%" colspan="3"></th>
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 3%">DATE</th>
                    <td style="border: 1px solid gray;padding: 8px; width: 15%;"></th>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>