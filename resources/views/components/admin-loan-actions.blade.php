@if($loan->status == 1)
<button class="btn btn-sm btn-primary" disabled>
    Active
</button>

<a class="btn btn-outline-danger" data-toggle="modal" data-target="#resolveCardModal">Resolve Card Details</a>
@endif

@if($loan->status == 2)
<button class="btn btn-sm btn-success" disabled>
    Fulfilled
</button>
@endif

@if($loan->status == 3)
<button class="btn btn-sm btn-danger" disabled>
    Inactive
</button>
@endif

@if ($loan->status == 0)
<!-- Here loan is still being processed -->
@if ($loan->canDisburse())
<p class="">* This loan is ready for this disbursement</p>
<p>
  
    <a class="btn btn-xs btn-warning"
        onclick="return confirm('Going ahead with this means every requirement has been met and the customer has recieved his money. Do you wish to proceed?')"
        href="{{ route('admin.loans.disburse', ['loan' => $loan->reference]) }}">
        Disburse Loan
    </a>

    <a class="btn btn-xs btn-danger"
    onclick="return confirm('Going ahead with this means no money would be paid to this client. Do you wish to proceed?')"
     href="{{ route('admin.loans.disburse-backend', ['loan' => $loan->reference]) }}">
        Disburse Loan (Backend)
    </a>
</p>
@else

<!-- Cant disburse yet. One or more of the collection methods might be pending -->

@endif

@endif

@php($collectionMethods = json_decode($loan->collection_methods) ?? [])

@foreach($collectionMethods as $method)

@switch ($method->code)
@case (App\Helpers\Constants::DDM_REMITA)
<!-- DDM REMITA -->
<h6><small>DDM REMITA ({{ $method->type }})</small></h6>
@if ($method->status == 0)
<div>
    <a class="btn btn-xs btn-primary"
        href="{{ route('admin.loans.pending.retry', ['loan' => $loan->reference, 'code' => $method->code]) }}">
        Retry REMITA Setup
    </a>
</div>

@elseif ($method->status == 1)
<div>
    <button disabled class="btn btn-xs btn-dark">
        Mandate Inactive
    </button>
    <a class="btn btn-xs btn-primary" href="{{ route('admin.loans.mandate-status', ['loan' => $loan->reference]) }}">
        Check DDM Status
    </a>

</div>
<!-- Only show this if otp activation is enabled -->
@if(config('remita.otp_activation_enabled'))
    @php($otpEnabled = config('remita.otp_activation_enabled'))
    <remita-otp-form 
        :bank="{{$loan->user->bankDetails->first()}}" 
        :loan="{{$loan}}"
        :mandateurl="'{{$loan->mandateUrl}}'"
        :otpenabled="{{json_encode($otpEnabled)}}">
        </remita-otp-form>
@endif
<div class="mt-1">
    <p>
        <a class="btn btn-link" target="_blank" href="{{ $loan->mandateUrl }}">
            * Print Mandate Here
        </a>
    </p>
</div>

@elseif ($method->status == 2)
<button class="btn btn-xs btn-success" disabled>
    Remita Mandate Activated
</button>

<a class="btn btn-xs btn-warning" href="{{ route('admin.loans.mandate-stop', ['loan' => $loan->reference]) }}">
    Stop Mandate
</a>

<a class="btn btn-xs btn-primary"  target="_blank" href="{{ route('admin.loans.mandate-history', ['loan' => $loan->reference]) }}">
    Mandate History
</a>
@endif
<br /><br />
@break
@case (App\Helpers\Constants::DDM_OKRA)
    <h6><small>DDM OKRA ({{ $method->type }})</small></h6>
    @if ($method->status == 0)
    <button disabled class="btn btn-xs btn-primary">
        <h6>Okra Setup Pending<h6>
    </button>
    @elseif ($method->status == 2)
        <h6>Okra Setup Activated<h6>
    @endif
@break
@case (App\Helpers\Constants::DDM_MONO)
    <h6><small>DDM MONO ({{ $method->type }})</small></h6>
    @if ($method->status == 0)
    <button disabled class="btn btn-xs btn-primary">
        <h6>Mono Setup Pending<h6>
    </button>
    @elseif ($method->status == 2)
        <h6>Mono Setup Activated<h6>
    @endif
@break
@case (App\Helpers\Constants::DAS_REMITA)
<!-- DAS REMITA -->

@break
@case (App\Helpers\Constants::DAS_IPPIS)
@case (App\Helpers\Constants::DAS_RVSG)

@php($type = $method->code === App\Helpers\Constants::DAS_IPPIS ? 'ippis': 'rvsg')

<h6><small>{{ strtoupper($type) }} DAS ({{ $method->type }})</small></h6>
<!-- DAS IPPIS -->
@if ($method->status == 1)
<a class="btn btn-xs btn-warning"
    onclick="return confirm('Going ahead with this means every requirement from the customer for IPPIS has been met. Do you wish to proceed?')"
    href="{{ route('admin.loans.mandate-approve', ['loan' => $loan->reference, 'type' => $type]) }}">
    Approve Mandate
</a>
<a class="btn btn-xs btn-danger" onclick="return confirm('Do you wish to proceed?')"
    href="{{ route('admin.loans.mandate-decline', ['loan' => $loan->reference, 'type' => $type]) }}">
    Decline Mandate
</a>
<p><em><small>
            * Check the Loan documents section for the customer's Authority Document.
        </small></em></p>
@elseif ($method->status == 2)
<button class="btn btn-xs btn-success" disabled>
    Mandate Approved
</button>
@elseif ($method->status == 4)
<button class="btn btn-xs btn-danger" disabled>
    Mandate Declined
</button>
@endif

<p>
    View customer authority document
    <a href="{{ route('admin.loans.mandate-form', ['loan' => $loan->reference, 'type' => $type]) }}" target="_blank">
        here.
    </a>
</p>
<br /><br />

@break
@endswitch

@endforeach

{{--
@if($loan->status != 0 && $loan->status != 2)
    <a class="btn btn-sm btn-warning"
        onclick="return confirm('Do you want to fulfil this loan?')"
        href="{{ route('admin.repayments.stop', ['loan' => $loan->reference]) }}">
Stop Collection
</a>
@endif
--}}

{{--
@if($loan->disburse_status == 1)
    <a class="btn btn-sm btn-primary"
        href="{{ route('admin.loans.mandate-status', ['loan' => $loan->reference]) }}">
Check Mandate Status
</a>

<a class="btn btn-sm btn-info" target="_blank" href="{{ $loan->mandateUrl }}">
    Get Mandate
</a>
@elseif($loan->disburse_status == 2)
<a class="btn btn-sm btn-success" href="{{ route('admin.loans.disburse', ['loan' => $loan->reference]) }}">
    Disburse Loan
</a>

<a class="btn btn-sm btn-danger" href="{{ route('admin.loans.disburse-backend', ['loan' => $loan->reference]) }}">
    Disburse Loan
</a>
@endif
--}}







@switch($loan->status)
    @case (1) 
    @php($employer = optional($loan->loanRequest ? $loan->loanRequest->employment : null)->employer)

    @if($loan->canRestructure() && isset($employer))

        <h4 class="card-title bold" style="text-transform:uppercase">Loan Restructure Section</h4>
        <restructure-loan 
            :loan="{{$loan}}" 
            :rate="{{$loan->interest_percentage}}"
            :unpaidemi="{{$loan->getUnpaidPrincipal()}}"
            :url="'{{route('admin.loans.restructure',['loan'=>$loan->reference])}}'"
            :token="'{{csrf_token()}}'"
            :oldemi="{{$loan->emi()}}"
            :collection_url="'{{route('admin.loans.setup.loan', ['loan' => $loan->reference])}}'"
            :collection_plans = "{{json_encode(config('settings.collection_methods'))}}"
            :employer_primary_collection_plan = "{{ $employer->collection_plan ?? '\'\'' }}"
            :employer_secondary_collection_plan = "{{ $employer->collection_plan_secondary ?? '\'\'' }}"
            :unpaidplans="{{$loan->repaymentPlans->where('status',false)->count()}}">
        </restructure-loan>

    @endif

    <br>

    <h4 class="card-title bold" style="text-transform:uppercase">Loan Penalty Settings</h4>
    <br>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#penalty-settings">Penalty Settings</button>
    
    @break
@endswitch



@if($loan->hasPenaltySettings() && !$loan->is_penalized)
    <form style="display:inline;" action="{{route('admin.buildup-penalty', ['loan'=>$loan->reference])}}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger btn-xs">Build Up Penalty</button>
    </form>
@endif

 <!-- Modal -->
 <div class="modal fade" id="penalty-settings" tabindex="-1" role="dialog" aria-labelledby="penaltySettingsLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h3 class="modal-title" id="loanRestructureLabel">Penalty Setting</h3>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <penalty-settings :setting="{{json_encode($loan->penaltySetting ?? new stdClass)}}" :entity_type="'Loan'" :entity_id="{{$loan->id}}"></penalty-settings>
                
            </div>

            <div class="modal-footer">
                
            </div>

        </div>
    </div>

</div>

<div class="modal fade" id="resolveCardModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Refer Loan: {{$loan->reference}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            <div class="modal-body">
                @if ($loan->user->billingCards->last())
                    <resolve-card :user_reference="'{{$loan->user->reference}}'"></resolve-card>
                @else
                    <span class="text-danger">No Card details found on this account</span>
                @endif 
            </div>

          
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@if($loan->canSettle())
    <br>
    <br>
    <h4 class="card-title bold" style="text-transform:uppercase">Loan Settlement Section</h4>

    @if(isset($loan->settlement) && $loan->settlement->status == 1)
        <button class="btn btn-primary" disabled> Processing</button>
        <a href="{{route('settlement.invoice.view',['reference'=>$loan->settlement->reference])}}" target="_blank"
            class="btn btn-primary btn-sm">
            View document
        </a>
    @elseif(isset($loan->settlement) && $loan->settlement->status == 3)
        <span class="btn btn-danger" disabled> Declined</span>
        <a href="{{route('settlement.invoice.view',['reference'=>$loan->settlement->reference])}}" target="_blank"
            class="btn btn-primary btn-sm">
            View document
        </a>
        <br>
        <br>
        <div class="alert alert-danger"> <b><i class="fa fa-warning"></i>Reason: </b>{{$loan->settlement->status_message}}</div>
    @elseif(isset($loan->settlement) && $loan->settlement->status == 2)
        <span class="btn btn-success"> Confirmed</span><br>
        <a href="{{route('settlement.invoice.view',['reference'=>$loan->settlement->reference])}}" target="_blank"
            class="btn btn-primary btn-sm">
            View document
        </a>
    @else
        @php($url = request()->user('admin') ? route('admin.pay.settlement',['reference'=>$loan->reference]) :
        route('staff.pay.settlement',['reference'=>$loan->reference]))
        <div class="modal-body" style="display:inline;padding-left:0px;">
            <form method="POST" action="{{$url}}" style="display:inline">
                {{csrf_field()}}
                <input type="hidden" name="amount" value="{{$loan->settlement_total * 100}}">
                <input type="hidden" name="email" value="{{$loan->user->email}}">
                <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                <input type="hidden" name="metadata"
                    value="{{ json_encode($array = ['loan_reference' => $loan->reference,]) }}">

                <button type="submit" class="btn btn-danger btn-sm">Settle (Paystack (charge ₦{{number_format(paystack_charge($loan->settlement_total))}}))</button>

            </form>
        </div>
    @endif

@endif

<!-- Loan settlement ends here -->