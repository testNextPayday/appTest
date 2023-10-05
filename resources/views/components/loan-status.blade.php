@if($loan->canTopUp())
<span class="btn btn-sm btn-success" class="btn btn-info btn-lg" data-toggle="modal" data-target="#topUp">
    <i class="fa fa-dot-circle-o"></i>
    Top Up Loan
</span>
@endif

@if($loan->status == 0)
<span class="btn btn-sm btn-warning">
    <i class="fa fa-spinner"></i>
    Pending
</span>
@elseif($loan->status == 1)
<span class="btn btn-sm btn-primary">
    <i class="fa fa-dot-circle-o"></i>
    Active
</span>

@elseif($loan->status == 2)
<span class="btn btn-sm btn-success">
    <i class="fa fa-check"></i>
    Fulfilled
</span>
@elseif($loan->status == 5)
<span class="btn btn-sm btn-primary diabled">
    <i class="fa fa-check"></i>
    Managed
</span>
@elseif($loan->status == 3)
<span class="btn btn-sm btn-danger">
    <i class="fa fa-close"></i>
    Cool-off
</span>
@else
<span class="btn btn-sm btn-danger">
    <i class="fa fa-close"></i>
    Defaulting
</span>
@endif