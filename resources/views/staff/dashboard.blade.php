@extends('layouts.staff-new')

@section('content')
    @php
        $staff = auth('staff')->user();
    @endphp
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12 d-flex align-items-center justify-content-between">
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    
        <div class="row">
            <div class="col-12 card-statistics">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-4 grid-margin stretch-card card-tile">
                        <div class="card">            
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    <h5>{{strtoupper($staff->reference)}}</h5>
                                    <i class="icon-user"></i>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-muted">Reference Number</p>
                                </div>
                                <div class="progress progress-md">
                                    <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 grid-margin stretch-card card-tile">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    <h5>ACTIVE</h5>
                                    <i class="icon-check"></i>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-muted">Status</p>
                                    <!--<p class="text-muted">max: 120</p>-->
                                </div>
                                <div class="progress progress-md">
                                    <div class="progress-bar bg-success w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 grid-margin stretch-card card-tile">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    <h5>{{$staff->users()->count()}} </h5>
                                    <i class="icon-people"></i>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-muted">Accounts</p>
                                    <!--<p class="text-muted">max: 54</p>-->
                                </div>
                                <div class="progress progress-md">
                                    <div class="progress-bar bg-primary w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <div class="col-12 col-sm-6 col-md-4 grid-margin stretch-card card-tile">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    @php
                                        $funds = $staff->funds()->whereNull('original_id');
                                    @endphp
                                    <h5>{{strtoupper($funds->count())}} ( ₦{{number_format($funds->sum('amount'), 2)}} )</h5>
                                    <i class="icon-present"></i>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-muted">Loans Funded</p>
                                </div>
                                <div class="progress progress-md">
                                    <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 grid-margin stretch-card card-tile">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    @php
                                        $acquiredFunds = $staff->funds()->whereNotNull('original_id');
                                    @endphp
                                    <h5>{{ $acquiredFunds->count() }} ( ₦{{number_format($acquiredFunds->sum('amount'), 2)}} )</h5>
                                    <i class="icon-basket-loaded"></i>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-muted">Loans Acquired</p>
                                    <!--<p class="text-muted">max: 120</p>-->
                                </div>
                                <div class="progress progress-md">
                                    <div class="progress-bar bg-primary w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 grid-margin stretch-card card-tile">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    @php
                                        $loanRequests = $staff->loanRequests(); 
                                    @endphp
                                    <h5>{{$loanRequests->count()}} ( ₦{{number_format($loanRequests->sum('amount'), 2)}} )</h5>
                                    <i class="icon-notebook"></i>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-muted">Loan Requests Placed</p>
                                    <!--<p class="text-muted">max: 54</p>-->
                                </div>
                                <div class="progress progress-md">
                                    <div class="progress-bar bg-danger w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
              
            </div>
            <div class="col-md-5 grid-margin stretch-card">
    
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
@endsection

@section('page-js')
<script src="{{asset('coreui/js/views/main.js')}}"></script>
@endsection