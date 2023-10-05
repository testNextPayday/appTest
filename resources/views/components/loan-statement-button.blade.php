@if($loan->repaymentPlans->count() > 0)
<div class="dropdown" style="display:inline-block">
    <a href="#" style="color:white;text-decoration:none" class="dropdown-toggle btn bg-info" data-toggle="dropdown" aria-expanded="false" aria-haspopup="true" id="loan_statement">Loan Statement</a>

    <div class="dropdown-menu" aria-labelledby="loan_statement">
        <a class="dropdown-item" href="{{route('view.loan.statement',['reference'=>$loan->reference])}}" target="_blank">View PDF</a>
        <a class="dropdown-item" href="{{route('download.loan.statement',['reference'=>$loan->reference])}}">Download as PDF</a>
        <a class="dropdown-item" href="{{route('mail.loan.statement',['reference'=>$loan->reference])}}">Send as Mail</a>
    </div>
</div>
@endif