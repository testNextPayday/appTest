@extends('layouts.admin-new')

@section('content')

<div class="content-wrapper">
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h4 class="page-title">Request {{$loanRequest->reference}}</h4>
            @component('components.admin-lr-status', ['loanRequest' => $loanRequest])
            @endcomponent
        </div>
    </div>
    
    @include('layouts.shared.error-display')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Remita Salary Information
                </div>
                <div class="card-body">
                    @component('components.salary-data', [
                        'gotValidData' => $gotValidData,
                        'salaryData' => $salaryData
                    ])
                    @endcomponent
                </div>
            </div>
        </div>
        <!--/.col-->
    </div>
    <br/>
    
    @if($loanRequest)
        @component('components.admin-lr-statistics', ['loanRequest' => $loanRequest])
        @endcomponent
        
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="card-title">ADMIN ACTION</h4>
                        <br/>
                        
                        @if($loanRequest->status == 1 || $loanRequest->status == 0)

                            @php
                        
                                $employer = @$loanRequest->employment->employer;
                        
                            @endphp
                            
                                
                            <a data-toggle="modal" data-target="#approveRequestModal" class="btn btn-outline-success">
                                Approve ({{ $gotValidData ? "DAS" : "DDM"}})
                            </a>
                            <a onclick="return confirm('Are you sure you want to decline this loan?');" 
                                class="btn btn-outline-danger"
                                href="{{route('admin.loan-requests.decline', ['reference' => $loanRequest->reference])}}">
                                Decline Request
                            </a>
                                
                            @if(!$gotValidData)
                                <br/>
                                <em><small><b>NB: Accepting this loan will set this customer up for DDM</b></small></em>
                            @endif
                            @if($loanRequest->status == 0)
                                <br/>
                                <em><small><b>NB: This action will override employer consent</b></small></em>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <br/>
    @endif
    
</div>

@if($loanRequest)
    <div class="modal fade" id="approveRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-primary" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Approve Loan Request: {{$loanRequest->reference}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="POST" action="{{route('admin.loan-requests.approve')}}">
                <div class="modal-body">
                    <div class="card-body">
                        {{ csrf_field() }}
                        <input type="hidden" name="request_id" value="{{$loanRequest->id}}"/>
                        <input type="hidden" name="collection_plan" value="{{$gotValidData ? 1 : 0}}"/>
                        <div class="form-group">
                            <label for="risk_rating">Select Risk Rating</label>
                            <select class="form-control" id="risk_rating" name="risk_rating" required>
                                <option value="5">5</option>
                                <option value="4">4</option>
                                <option value="3">3</option>
                                <option value="2">2</option>
                                <option value="1">1</option>
                            </select> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Approve Loan Request</button>
                </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endif
@endsection

@section('page-js')
@endsection