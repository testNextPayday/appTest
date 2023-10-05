@if($loanRequest->status == 0 || $loanRequest->status == 1)
    <button type="button" class="btn btn-danger btn-sm">Pending</button>
@elseif($loanRequest->status == 2)
    <!-- Place buttons for accepting/rejecting loans when loan is complete or due-->
    @if($loanRequest->percentage_left <= 0 || (Carbon\Carbon::now()->gt($loanRequest->expected_withdrawal_date) && $loanRequest->amountRealized > 0))
        <a class="btn btn-outline-success btn-sm"
            onclick="return confirm('Are you sure?');"
            href="{{route($prefix . '.loan-requests.accept-funds', ['loanRequest' => $loanRequest->reference])}}">
            Accept Funds
        </a>

        <a onclick="return confirm('Are you sure you want to reject this loan?');" 
            class="btn btn-outline-danger btn-sm"
            href="{{route($prefix . '.loan-requests.cancel', ['loanRequest' => $loanRequest->reference])}}">
            Reject Funds
        </a>
    @else
        @if(!$loanRequest->investor_id || $loanRequest->amountRealized < 1)
        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#assignRequest">Assign Request</button>
        @else
        <button type="button" class="btn btn-sm btn-dark" disabled>Assigned</button>
        @endif
        <a onclick="return confirm('Are you sure you want to cancel this request?');" 
            class="btn btn-outline-danger btn-sm"
            href="{{route($prefix . '.loan-requests.cancel', ['loanRequest' => $loanRequest->reference])}}">
            Cancel Request
        </a>
    @endif
@elseif($loanRequest->status == 3)
    <button type="button" class="btn btn-default btn-sm">Cancelled</button>
@elseif($loanRequest->status == 4)
    <button type="button" class="btn btn-success btn-sm">Taken</button>
    @if($loanRequest->loan)
        <a href="{{route($prefix . '.loans.view', ['reference' => $loanRequest->loan->reference])}}"
            class="btn btn-primary btn-sm">View Loan
        </a>
    @else
    <button type="button" class="btn btn-primary btn-sm">Processing Loan</button>
    @endif
@elseif($loanRequest->status == 5)
    <button type="button" class="btn btn-danger btn-sm">Declined - Employer</button>
@elseif($loanRequest->status == 6)
    <button type="button" class="btn btn-danger btn-sm">Declined - Admin</button>
@elseif($loanRequest->status == 7)
    @php
        if (auth('affiliate')->check()) {
           
            $url = route('affiliates.loan-requests.resubmit', ['loanRequest'=>$loanRequest->reference]);
        }elseif(auth()->check()) {
            
            $url = route('users.loan-requests.resubmit', ['loanRequest'=>$loanRequest->reference]);
        }else {
            $url = '#';
        }

    @endphp
    <resubmit-loan-request :loanrequest="{{json_encode($loanRequest->load('employment.employer'))}}" :url="'{{$url}}'" :token="'{{csrf_token()}}'">
    </resubmit-loan-request>
    <button type="button" class="btn btn-warning btn-sm">Referred - {{$loanRequest->decline_reason}}</button>
@else
    <button type="button" class="btn btn-warning btn-sm">Unknown</button>
@endif