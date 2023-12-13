@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">
                {{ $employer->name}}&nbsp;&nbsp;
                @component('components.employer-status', ['status' => $employer->is_verified])
                @endcomponent
            </h4>
            <div class="pull-right">
                <form method="post" action="{{route('admin.employers.setStatus')}}">
                    {{csrf_field()}}
                    <div class="form-group">
                            <label class="control-label" for="verified_employer">
                                <strong>Set Verification Status</strong></label>
                            <div class="input-group mb-3">
                                <input type="hidden" name="employer_id" value="{{$employer->id}}">
                                <select name="status" class="form-control"> 
                                    <option value="0">Unverified</option>
                                    <option value="1">Under Verification</option>
                                    <option value="2">Verification Denied</option>
                                    <option value="3">Verified</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">Save</button>
                            </div>    
                        </div>
                </form>
            </div>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4>Employer Data</h4>
                            <br/>
                            <br/>
                        </div>
                        <div class="col-sm-3">
                            <div class="text-center">
                                <img src="{{asset(Storage::url('public/defaults/avatars/default.png'))}}" style="width: 120px"/>
                            </div>
                            <hr/>
                            <div class="text-center">
                                <a href="{{ route('admin.employers.employees', ['employer' => $employer->id]) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="icon-people"></i> Employees
                                </a>
                                <a href="{{ route('admin.employers.employees.loans', ['employer' => $employer->id])}}"
                                    class="btn btn-sm btn-success">
                                    <i class="icon-calculator"></i> Loans
                                </a>
                            </div>
                            <br/>
                        </div>
                        <div class="col-sm-4 px-3">
                            <p>Name: <strong>{{$employer->name}}</strong></p>
                            <p>Email: <strong>{{$employer->email}}</strong></p>
                            <p>Phone: <strong>{{$employer->phone}}</strong></p>
                            <p>Address: <strong>{{$employer->address}}</strong></p>
                            <p>State: <strong>{{$employer->state}}</strong></p>
                            <p>Payment Date: <strong>{{$employer->payment_date}}</strong></p>
                            <p>Payment Mode: <strong>{{$employer->payment_mode == 1 ? 'Bank Transfer':'E-Transfer'}}</strong></p>
                            
                            <p>Approver's Name: <strong>{{$employer->approver_name}}</strong></p>
                            <p>Approver's Phone: <strong>{{$employer->approver_phone}}</strong></p>
                            <p>Approver's Email: <strong>{{$employer->approver_email}}</strong></p>
                            <p>Approver's Designation: <strong>{{$employer->approver_designation}}</strong></p>
                        </div>
                        
                        <div class="col-sm-4">
                            <p>3 Months Rate: <strong>{{$employer->rate_3}}%</strong></p>
                            <p>6 Months Rate: <strong>{{$employer->rate_6}}%</strong></p>
                            <p>12 Months Rate: <strong>{{$employer->rate_12}}%</strong></p>
                            <br/>
                            <p>3 Months Fees: <strong>{{$employer->fees_3}}%</strong></p>
                            <p>6 Months Fees: <strong>{{$employer->fees_6}}%</strong></p>
                            <p>12 Months Fees: <strong>{{$employer->fees_12}}%</strong></p>
                            <p>Max Tenure: <strong>{{$employer->max_tenure}} Months</strong></p>
                            <p>Collection VAT Fee: <strong>{{$employer->vat_fee}} %</strong></p>
                            <p>Loan VAT Fee: <strong>{{$employer->loan_vat_fee}} %</strong></p>
                            <p>Interest VAT Fee: <strong>{{$employer->interest_vat_fee}} %</strong></p>
                            <br/>
                            <p>Collection Plans:</p> 
                            <p>
                                <span class="badge badge-primary">Primary</span>
                                <strong>{{$employer->displayCollectionPlan()}}</strong>
                            </p>
                            <p>
                                <span class="badge badge-danger">Secondary</span>
                                <strong>{{$employer->displayCollectionPlan("secondary")}}</strong>
                            </p>
                            <br/>
                            <a href="{{ route('admin.employers.manage', ['employer' => $employer->id]) }}"
                                class="btn btn-sm btn-warning">
                                <i class="icon-info"></i> Edit
                            </a>
                        </div>
                        
                    </div>
                    <br/>
                </div>
            </div>
        </div>
        <!--/.col-->
    </div>
    
    <br/>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4>Sweep Parameters</h4>
                        <a href="#updateCollectionParams" data-toggle="modal" class="btn btn-primary btn-sm text-white">Modify Parameters</a>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-sm-6">
                            <h5>Sweep Start Day: <strong>{{ $employer->sweep_start_day }}</strong></h5>
                            <h5>Sweep End Day: <strong>{{ $employer->sweep_end_day }}</strong></h5>  
                            <h5>Sweep Frequency: <strong>{{ $employer->sweep_frequency}} times daily</strong></h5>  
                        </div>
                        <div class="col-sm-6">
                            <h5>Peak Start Day: <strong>{{ $employer->peak_start_day }}</strong></h5>
                            <h5>Peak End Day: <strong>{{ $employer->peak_end_day }}</strong></h5>  
                            <h5>Peak Frequency: <strong>{{ $employer->peak_frequency}} times daily</strong></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br/>
        
    @component('components.modals.sweep_params_modifier', ['model' => $employer])
    @endcomponent
    
    <div class="row">
      <div class="col-md-12">
         <div class="card">
           <div class="card-body">
              <div class="row">
                  <div class="col-md-12">
                      <form class="form" method="post" action="{{route('admin.loan.limit', Request::segment(4))}}">
                        @csrf
                        <div class="form-group">
                            <label for="loanlimit"><strong>Loan limit settings</strong></label>
                          <input type="text" name="loanlimit" id="loanlimit" placeholder="Enter loan limit" value="{{$employer->loan_limit}}" class="form-control" required> 
                        </div>

                        <div class="form-group">
                            <label for="application_fee"><strong>Application fee (LoanRequest Form)</strong></label>
                            <input type="text" class="form-control" name="application_fee" id="application_fee" value="{{$employer->application_fee}}" required>
                        </div>

                        <div class="form-group">
                            <label for="success_fee"><strong>Success, Transaction, Insurance & Interest fee (%)</strong></label>
                          <input type="text" name="success_fee" id="success_fee" value="{{$employer->success_fee}}" class="form-control" required> 
                        </div>

                        <div class="form-group float-right">
                            <button class="btn btn-info">Save</button>
                        </div>
                      </form>
                  </div>
              </div>
            </div>
         </div>
       </div>
    </div>
    
    <br>
    <documents-required :user_id={{Request::segment(4)}} :settings="{{$requireDocs}}" ></documents-required>
    <br>
    {{-- <loanrequest-docs :user_id={{Request::segment(4)}} :loandocs="{{$loanRequestDocs}}" 
    :loansettings="{{$loansettings}}" 
    :capitalize="{{$employer->is_capitalized}}" 
    :upgrade="{{$employer->upgrade_enabled}}" 
    :upfrontinterest="{{$employer->upfront_interest}}" 
    :repayment="{{$employer->affiliate_payment_method}}"
    :enableguarantor={{ $employer->enable_guarantor }}
    >    
    </loanrequest-docs> --}}
    <br>
    <penalty-settings :setting="{{json_encode($employer->penaltySetting ?? new stdClass)}}" :entity_type="'Employer'" :entity_id="{{$employer->id}}"></penalty-settings>
    @if($employer->penaltySetting)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Penalty Actions</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" style="display:inline;" action="{{route('admin.buildup-penalty-employer', ['id'=>$employer->id])}}">
                            @csrf
                            <button class="btn btn-sm btn-danger" type="submit">Build Penalty</button>
                        </form>

                        <form method="POST" style="display:inline;" action="{{route('admin.dissolve-penalty-employer', ['id'=>$employer->id])}}">
                            @csrf
                            <button class="btn btn-sm btn-primary" type="submit">Dissolve Penalty</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        
    @endif
</div>

@endsection

@section('page-js')
@endsection