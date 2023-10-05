<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Loan Statement</title>
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
      width: 100%;
      font-size: 80%;
    }
  </style>
</head>

<body style="margin:0; padding: 0;  font-family: 'Century Gothic', sans-serif">
  <div style="width: 98%;  margin:auto">
    <div>
      <div style="display:inline-block;width: 60px; height: 60px; width: 90%; margin-left: 70px; text-align: center;">
        <img src="{{ $_SERVER['DOCUMENT_ROOT'].'/logo_pack/icon/colored/icon_colored_128.png' }}" style="width:100px; height: 100px; " />
      </div>
      <br>

      <h1 style="text-align:center;border:1px solid black;letter-spacing:5px;"> Client Statement </h1>

    </div>
  
    <div>
      <span style="float:right;clear:both">
        <span><b>Date:</b> {{now()->toDateString()}}</span>
        <br />
        <span>{{$loan->user->name}}</span>
      </span>
      <span>
        <img src="{{ $_SERVER['DOCUMENT_ROOT'].'/logo_pack/logo/colored/32.png' }}" />
      </span>

    </div>

    <!-- New Repayment starts here -->
   

    @if(optional($loan->settlement)->status == 2)

      @component('components.loan_statements.with_settled',['loan'=>$loan])
      @endcomponent

    @elseif($loan->repaymentPlans->first()->is_new)

      @component('components.loan_statements.with_armotised',['loan'=>$loan])
      @endcomponent

    @else
    
      @component('components.loan_statements.with_old',['loan'=>$loan])
      @endcomponent

    @endif
  </div>

</body>

</html>