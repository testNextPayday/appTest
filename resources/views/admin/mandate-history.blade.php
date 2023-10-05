<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Mandate History </title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }

    table th {
      border: 1px solid black;
      font-size: 87%;
    }

    table td {
      border: 1px solid black;
      padding: 5px;
      font-size: 80%;
    }
  </style>
</head>

<body style="margin:0; padding: 0;  font-family: 'Century Gothic', sans-serif">


  <div style="width: 80%;  margin:auto">
    <div>
      <div style="display:inline-block;width: 60px; height: 60px; width: 90%; margin-left: 70px; text-align: center;">
        <img src="{{ asset('logo_pack/icon/colored/icon_colored_128.png') }}" style="width:100px; height: 100px; " />
      </div>
      <br>

      <h1 style="text-align:center;border:1px solid black;letter-spacing:5px;"> Mandate History #{{$response->mandateId}} </h1>

    </div>

  </div>

  <div>
  <!-- <h3 style="text-align: center;text-transform: uppercase">Loan terms</h3> -->
  <!-- div for loan terms -->
  <div>
    <table style="width:80%;border:1px solid black; margin:auto;" id="loan_terms">
      <tbody>
        <tr>
        
          <td>
            <div>
              <span style="float:right">{{$response->requestId}}</span>
              <span><b>Request#</b></span>
            </div>
          </td>
        </tr>

        <tr>
            <td>

                <div>
                    <div style="float:right;">{{$response->mandateId}}</div>
                    <div><b>Mandate ID</b></div>
                </div>
            </td>
        </tr>
        <tr>
          <td>
            <div>
              <span style="float:right">{{$response->totalTransactionCount}}</span>
              <span><b>Total Transactions</b></span>
            </div>
          </td>
        </tr>
       
       
      </tbody>
    </table>
  </div>



  <!-- div for transaction statement for wallet -->
  <div>
    <h3 style="text-transform: uppercase;text-align:center">Activities on Mandate</h3>
    <table style="width:80%;margin:auto;">
      <thead>
        <tr>
          <th>Date</th>
          <th>Amount</th>
          <th>Transaction Reference</th>
          <th>RRR</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
            @forelse($response->history as $buffer)
                <tr>
                    <td>{{$buffer->lastStatusUpdateTime}}</td>
                    <td>{{$buffer->amount}}</td>
                    <td>{{$buffer->transactionRef}}</td>
                    <td>{{$buffer->RRR}}</td>
                    <td>{{$buffer->status}}</td>
                </tr>
            @empty
                <p>No transactions </p>
            @endforelse
      </tbody>

    </table>
  </div>

</div>

</body>

</html>