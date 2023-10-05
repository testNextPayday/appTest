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

@if ($loan->status == 0)
<!-- Here loan is still being processed -->
    @if ($loan->canDisburse())
        <p class="badge badge-dark" disabled>
            * You are not required to do anything at this point
        </p>
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
                            <a href="{{ route('affiliates.loans.authorityForm', ['loan' => $loan->reference, 'type' => $type]) }}"
                                target="_blank">
                                Here.
                            </a>
                            Use the Form below to upload when you are done.
                        </p>
                        <br/>
                        <form class="row" method="post"
                            enctype="multipart/form-data"
                            action="{{ route('affiliates.loans.authorityForm.upload', ['loan' => $loan->reference]) }}">
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
