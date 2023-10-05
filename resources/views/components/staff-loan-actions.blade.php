@php($staff = request()->user('staff'))
@if($loan->status == 1)
    <button class="btn btn-xs btn-primary" disabled>
       Loan is Active
    </button>
@endif

@if($loan->status == 2)
    <button class="btn btn-xs btn-success" disabled>
       Loan has been Fulfilled
    </button>
@endif

@if($loan->status == 3)
    <button class="btn btn-xs btn-danger" disabled>
        Defaulting Loan
    </button>
@endif

@if ($loan->status == 0 || $loan->status == 1)
<!-- Here loan is still being processed -->
    @if ($loan->canDisburse())

        @if ($staff->manages('loan_disbursement'))
            <a class="btn btn-xs btn-warning"
                onclick="return confirm('Going ahead with this means every requirement has been met and the customer has recieved his money. Do you wish to proceed?')"
                href="{{ route('staff.loans.disburse', ['loan' => $loan->reference]) }}">
                Disburse Loan
            </a>
        @else
            <p class="badge badge-dark" disabled>
                * You are not required to do anything at this point
            </p>
        @endif
    @else
    
        <!-- Cant disburse yet. One or more of the collection methods might be pending -->
        @php($collectionMethods = json_decode($loan->collection_methods) ?? [])
    
        @foreach($collectionMethods as $method)
            
            @switch ($method->code)
                @case (App\Helpers\Constants::DDM_REMITA)
                <!-- DDM REMITA -->
                    <h6>DDM REMITA ({{ $method->type }})</h6>
                    @if ($method->status == 1)
                        <button class="btn btn-sm btn-primary" disabled style="cursor:not-allowed">
                            Awaiting Mandate Activation
                        </button>
                        <a class="btn btn-sm btn-info" target="_blank"
                            href="{{ $loan->mandateUrl }}">
                            Get Mandate
                        </a>
                        <p><em>* You'll need to print the mandate and take it to your bank for activation. Ignore if you have done this already.</em></p>
                    @elseif ($method->status == 2)
                        <button class="btn btn-sm btn-success" disabled style="cursor:not-allowed">
                            Remita Mandate Activated
                        </button>
                    @endif
                    <br/><br/>
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
                @case (App\Helpers\Constants::DAS_REMITA)
                <!-- DAS REMITA -->
                
                    @break
                @case (App\Helpers\Constants::DAS_IPPIS)
                @case (App\Helpers\Constants::DAS_RVSG)
                    
                    @php($type = $method->code === App\Helpers\Constants::DAS_IPPIS ? 'ippis': 'rvsg')
                    
                    <h6>{{ strtoupper($type) }} DAS ({{ $method->type }})</h6>
                <!-- DAS IPPIS -->
                    @if ($method->status == 1)
                        <p>
                            Get your Authority Form 
                            <a href="{{ route('staff.loans.authorityForm', ['loan' => $loan->reference, 'type' => $type]) }}"
                                target="_blank">
                                Here.
                            </a>
                            Use the Form below to upload when you are done.
                        </p>
                        <br/>
                        <form class="row" method="post"
                            enctype="multipart/form-data"
                            action="{{ route('staff.loans.authorityForm.upload', ['loan' => $loan->reference]) }}">
                            @csrf                            
                            <div class="form-group col-sm-4">
                                <label>Upload Signed Authority Form</label>
                                <input type="file" name="authority_form" class="form-control" required/>
                                <br/>
                                <button class="btn btn-primary btn-xs">Upload</button>
                            </div>
                        </form>
                        <p><em><small>
                            * Ignore if this document shows up in your "Loan Documents Section"
                        </small></em></p>
                        <br/>
                    @elseif ($method->status == 2)
                        <button class="btn btn-xs btn-success" disabled style="cursor:not-allowed">
                            Mandate Approved
                        </button>
                    @elseif ($method->status == 4)
                        <button class="btn btn-xs btn-danger" disabled style="cursor:not-allowed">
                            Mandate Declined
                        </button>
                    @endif
                    <br/><br/>
                    @break
            @endswitch
            
        @endforeach
    @endif

@endif


@if( isset($staff) && $staff->manages('settlements') )

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

        @php($url = route('staff.pay.settlement',['reference'=>$loan->reference]) )
        <div class="modal-body" style="display:inline">
            <form method="POST" action="{{$url}}" style="display:inline">
                {{csrf_field()}}
                <input type="hidden" name="amount" value="{{$loan->settlement_total * 100}}">
                <input type="hidden" name="email" value="{{$loan->user->email}}">
                <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                <input type="hidden" name="metadata"
                    value="{{ json_encode($array = ['loan_reference' => $loan->reference,]) }}">

                <button type="submit" class="btn btn-danger">Settle (Paystack (charge ₦{{number_format(paystack_charge($loan->settlement_total))}}))</button>

            </form>
        </div>
    @endif

    @endif

@endif


@php($employer = optional($loan->loanRequest ? $loan->loanRequest->employment : null)->employer)

@if( isset($staff) && $staff->manages('loan_restructuring') )

    @if($loan->canRestructure() && isset($employer))

       <br>
       <br>
        <restructure-loan 
            :loan="{{$loan}}" 
            :rate="{{$loan->interest_percentage}}"
            :unpaidemi="{{$loan->getUnpaidRepayments()}}"
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

@endif
