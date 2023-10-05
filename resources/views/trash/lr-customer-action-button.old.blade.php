@if($loanRequest->status == 0 || $loanRequest->status == 1)
    <button type="button" class="btn btn-danger btn-sm">Pending</button>
@elseif($loanRequest->status == 2)
    <!-- Place buttons for accepting/rejecting loans when loan is complete or due-->
    @if($loanRequest->percentage_left <= 0 || Carbon\Carbon::now()->gt($loanRequest->expected_withdrawal_date))
        @if($loanRequest->collection_plan == 'DAS')
            <a class="btn btn-outline-success btn-sm"
                onclick="return confirm('Are you sure?');"
                href="{{route($prefix . '.loan-requests.funds.accept.das', ['loanRequest' => $loanRequest->reference])}}">
                Accept Loan (DAS)
            </a>
        @else
            @if($loanRequest->mandateStage == 0 && $loanRequest->percentage_left < 100)
                <a class="btn btn-outline-success btn-sm" data-toggle="modal"
                    data-target="#mandateExplanatoryModal">
                    Accept Loan (DDM)
                </a>
            @elseif($loanRequest->mandateStage == 1)
                <?php
                    $mandateId = $loanRequest->mandateId;
                    $requestId = $loanRequest->requestId;
                    $merchantId = config('remita.merchantId');
                    $apiKey = config('remita.apiKey');
                
                    $hash = hash('sha512', $merchantId.$apiKey.$requestId);
                    $redirectUrl = config('remita.baseUrl') . "ecomm/mandate/form/";
                    $redirectUrl .= "{$merchantId}/{$hash}/{$mandateId}/{$requestId}/rest.reg";
                ?>
                <a href="{{$redirectUrl}}" target="_blank" class="btn btn-info btn-sm">Get Mandate Form</a>
                <a onclick="return confirm('This will set up your repayment plan if mandate is active. Proceed?');" 
                    class="btn btn-outline-primary btn-sm" href="{{route($prefix . '.loan-request.mandate.check', ['loanRequest' => $loanRequest->reference])}}">
                    Check Mandate Status
                </a>
                @if($prefix == 'users')
                <a onclick="return confirm('Are you sure you want to authorize your mandate?');" 
                    class="btn btn-outline-success btn-sm"
                    href="{{route('users.loan-request.mandate.authorize', ['mandateId' => $mandateId, 'requestId' => $requestId])}}">
                    Activate Mandate Online
                </a>
                @endif
            @endif
        
        @endif
        
        <a onclick="return confirm('Are you sure you want to reject this loan?');" 
            class="btn btn-outline-danger btn-sm"
            href="{{route($prefix . '.loan-requests.cancel', ['reference' => $loanRequest->reference])}}">
            Reject Loan
        </a>
    @else
        <!--<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#editRequestModal">Modify Request</button>-->
        <a onclick="return confirm('Are you sure you want to cancel this request?');" 
            class="btn btn-outline-danger btn-sm"
            href="{{route($prefix . '.loan-requests.cancel', ['reference' => $loanRequest->reference])}}">
            Cancel Request
        </a>
    @endif
@elseif($loanRequest->status == 3)
    <button type="button" class="btn btn-default btn-sm">Cancelled</button>
@elseif($loanRequest->status == 4)
    <button type="button" class="btn btn-success btn-sm">Taken</button>
    <a href="{{route($prefix . '.loans.view', ['reference' => $loanRequest->loan])}}" class="btn btn-primary btn-sm">View Loan</a>
@elseif($loanRequest->status == 5)
    <button type="button" class="btn btn-danger btn-sm">Declined - Employer</button>
@elseif($loanRequest->status == 6)
    <button type="button" class="btn btn-danger btn-sm">Declined - Admin</button>
@else
    <button type="button" class="btn btn-warning btn-sm">Unknown</button>
@endif