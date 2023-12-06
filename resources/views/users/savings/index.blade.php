@extends('layouts.user')
@section('content')
    <main class="main">
        <div class="container-fluid mt-5">
            <div class="animated fadeIn">

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-primary text-light">
                                <h5>Virtual Account</h5>
                            </div>
                            <div class="card-body">
                                @if (Auth::user()->virtualAccount)
                                    <p style="line-height:10px">A/c No:
                                        <strong>{{ Auth::user()->virtualAccount->number }}</strong>
                                        <hr>
                                    </p>
                                    <p style="line-height:10px">Bank:
                                        <strong>{{ Auth::user()->virtualAccount->bank }}</strong>
                                        <hr>
                                    </p>
                                    <p style="line-height:10px">A/c Name:
                                        <strong>{{ Auth::user()->virtualAccount->name }}</strong>
                                    </p>
                                @endif
                                @if (!Auth::user()->virtualAccount)
                                    <form action="{{ route('create_virtual_account') }}" method="post">
                                        @csrf

                                        <button
                                            class="btn btn-md btn-success text-light ml-2 btn-lg waves-effect rounded-pill">
                                            &nbsp;
                                            <small><i class="fa fa-money"></i>
                                                Create Virtual Account</button></small>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 col-lg-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body pb-0">
                                <button type="button" class="btn btn-transparent p-0 float-right">
                                    <i class="icon-note"></i>
                                </button>
                                <h2 class="mb-0">{{ number_format(0, 2) }}</h2>
                                <p><a style="color:white" href="{{ route('users.loan-requests.index') }}">Current
                                        Savings</a></p>
                            </div>
                            <div class="chart-wrapper px-3" style="height:70px;">
                                <canvas id="card-chart1" class="chart" height="70"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 col-lg-4">
                        <div class="card text-white bg-success">
                            <div class="card-body pb-0">
                                <button type="button" class="btn btn-transparent p-0 float-right">
                                    <i class="icon-note"></i>
                                </button>
                                <h2 class="mb-0">{{ number_format(0, 2) }}</h2>
                                <p><a style="color:white" href="{{ route('users.loan-requests.index') }}">Total Interest on
                                        Savings</a></p>
                            </div>
                            <div class="chart-wrapper px-3" style="height:70px;">
                                <canvas id="card-chart2" class="chart" height="70"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#savingModal">
                            Create Savings plan
                        </button>
                    </div>
                </div>

                <!--/.col-->

                {{-- Recent Transaction log --}}


                <h4 class="mt-5 text-muted">Savings</h4>
                <hr>
                <div class="row">
                    @foreach ($savings as $saving)
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body p-3 clearfix">
                                <i class="fa fa-money bg-primary p-3 font-4xl mr-3 float-left"></i>
                            <div class="h5 text-primary mb-0 mt-2">Target: ₦ {{ number_format($saving->target_amount, 2) }}</div>
                            <div class="text-muted text-uppercase font-weight-bold font-xs">Current savings: {{ $saving->amount }}</div>
                            <div class="text-muted text-uppercase font-weight-bold font-xs">{{ $saving->name }}</div>
                            <div class="text-muted text-uppercase font-xs">Status
                                    {{-- &nbsp;
                                    @if ($loanRequest->status == 0)
                                    <span class="badge badge-danger">Inactive</span>
                                    @elseif($loanRequest->status == 7)
                                    <span class="badge badge-warning">Loan Request Referred</span>
                                    @endif --}}
                                    <span class="badge badge-success">Active</span>
                                </div>
                            </div>
                            <div class="card-footer px-3 py-2">
                            <a class="font-weight-bold font-xs btn-block text-muted" href="{{ route('users.savings.view', $saving->id) }}">
                                    View Request
                                    <i class="fa fa-angle-right float-right font-lg"></i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                   
                </div>
            </div>
        </div>
        <!-- /.conainer-fluid -->
    </main>




    <div class="modal fade" id="savingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="text-muted" id="exampleModalCenterTitle">Start New Savings Plan</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="conainer-fluid">

                        <div class="alert alert-success">
                    <p><strong>Note:</strong> Your wallet will be charged during your savings plan, ensure to make payments to your wallet</p>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form">
                                    <form action="{{ route('users.savings.store') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            @foreach ($errors->all() as $error)
                                                <p class="alert alert-danger">{{ $error }}</p>
                                            @endforeach
                                        </div>
                                        <div class="form-group">
                                            <label for="">Plan Name</label>
                                            <input type="text" name="name" class="form-control"
                                                placeholder="Plan name ">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Target amount</label>
                                                    <input type="number" name="target_amount" class="form-control"
                                                        placeholder="Target amount ">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">Target duration (in months)</label>
                                                    <input type="date" name="payback_date" class="form-control"
                                                        placeholder="Target duration ">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
    <script src="{{ asset('coreui/js/views/main.js') }}"></script>
    <script type="text/javascript">
        $(window).on('load', function() {
            $('#savingModal').modal('show');
        });
    </script>
@endsection
