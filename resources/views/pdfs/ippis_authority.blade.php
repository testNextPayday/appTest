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
                GLOBAL STANDING ORDER
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
            <table style="width: 120%; border-collapse: collapse; font-size: 90%">
                <tr style="color: gray; text-align: center;">
                    <th style="border: 1px solid #000;padding: 8px;">Equal Monthly <br/> Installment [EMI]</th>
                    <th style="border: 1px solid #000;padding: 8px;">Number of<br/>Deductions</th>
                    <th style="border: 1px solid #000;padding: 8px; width: 40%;">Purpose</th>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 115%; text-align: center; border: 1px solid #000;padding: 8px; height: 40px">N {{ number_format($emi, 2)}}</td>
                    <td style="font-weight: bold; text-align: center; border: 1px solid #000;padding: 8px;">{{ $deductions }}</td>
                    <td style="border: 1px solid #000;padding: 8px;">{{ $purpose }}</td>
                </tr>
            </table>
        </div>

        <div>
            <br/>
            <p style="text-align: center; color: gray; text-transform: uppercase; font-weight: bold; width: 120%;">
                Additional Info
            </p>
            <p style="border: 1px solid gray; padding: 1em; width: 115%; font-size: 90%; line-height: 28px; box-sizing: border-box;">
                This authority is issued in respect of the
                N {{ number_format($amount, 2)}} [ {{ ucwords($amountInWords) }} Naira] 
                nextpayday loan obtained from Nextpayday Limited
            </p>

            <br/>
            <p style="text-align: center; color: gray; text-transform: uppercase; font-weight: bold; width: 120%;">
                Authorization
            </p>
            <p style="color: gray; font-size: 85%; width: 120%; line-height: 28px;">
                I HEREBY AUTHORISE NEXTPAYDAY LIMITED OR ITS SUCCESSOR, PARTNER BANK, 
                RECOVERY AGENT OR ANY OTHER AGENT DULY ENGAGED BY NEXTPAYDAY TO 
                TRIGGER GLOBAL STANDING INSTRUCTION [GSI] ON ANY OUTSTANDING PRINCIPAL 
                AND INTEREST LEFT UNPAID UPON MATURITY OF THIS LOAN FROM ALL ACCOUNTS LINKED 
                TO MY BVN IN ANY PARTICIPATING FINANCIAL INSTITTION IN NIGERIA. 
            </p>
        </div>
        <br/>
        <div>
            <table border="0" style="width: 120%; border-collapse: collapse; border: 0">
                <tr style="color: black;">
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 12%">EMPLOYEE SIGNATURE</th>
                    <td style="border: 1px solid gray;padding: 8px; width: 20%" colspan="3">{{ $bvn }}</th>
                    <td style="font-size: 11px; border: 1px solid gray;padding: 8px; background: skyblue; width: 3%">DATE</th>
                    <td style="border: 1px solid gray;padding: 8px; width: 15%;"> {{ $reg_date}}</th>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>