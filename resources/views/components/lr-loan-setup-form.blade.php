<div class="row">
    <div class="col-sm-12">
        <h4>SETUP LOAN</h4>
        <hr/>
        
        @php($employer = optional($loanRequest->employment)->employer)

        
        <form method="post" action="{{ $url }}">
            {{ csrf_field() }}
            <div class="row justify-content-center">
                <div class="col-sm-8">
                    
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="badge badge-primary">Primary Collection</h5>
                        </div>
                        <br/><br/>
                    </div>
                    
                    <collection-plan-juggler
                        :collection-plans="{{ json_encode(config('settings.collection_methods'))}}"
                        :field-name="'collection_plan'"
                        :selected="{{ $employer->collection_plan ?? '\'\'' }}">
                    </collection-plan-juggler>
                    
                    <br/><br/>
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="badge badge-danger">Secondary Collection</h5>
                        </div>
                        <br/><br/>
                    </div>
                    <collection-plan-juggler
                        :collection-plans="{{ json_encode(config('settings.collection_methods'))}}"
                        :field-name="'collection_plan_secondary'"
                        :selected="{{ $employer->collection_plan_secondary ?? '\'\'' }}">
                    </collection-plan-juggler>
                    <br/><br/>


                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="badge badge-success">Optional Settings (Remita DDM) </h5>
                        </div>
                        <br/><br/>
                        @inject('collectionDates', '\App\Remita\DDM\CollectionDates')
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Mandate Start Date</label>
                            <input  class="form-control" type="date" name="startDate" value="{{$collectionDates->getStartDateHtml()}}">
                        </div>
                        
                        <div class="form-group col-sm-6">
                            <label>Mandate End Date</label>
                            <input  class="form-control" type="date" name="endDate" value="{{$collectionDates->getEndDateHtml($loanRequest)}}">
                        </div>
                    </div>
                    <br/><br/>
                    
                    <div class="form-group">
                        <label>Loan EMI (₦)</label>
                        <input type="text" class="form-control" placeholder="Enter loan EMI" 
                            name="emi" value="{{ old('emi') ?? $loanRequest->currentEmi() }}" required/>
                    </div>
                            
                </div>
                
            </div>
            <br/>
            <div class="text-right">
                <a class="btn btn-outline-info btn-sm"
                    href="{{ is_null(auth('staff')->user()) ? route('admin.users.das', ['user' => $loanRequest->user->reference]) : route('staff.users.das', ['user' => $loanRequest->user->reference]) }}"
                    target="_blank">Check Remita (DAS) Eligibility</a>
                <button class="btn btn-danger">Setup Loan</button>
            </div>
        </form>
    </div>
</div>