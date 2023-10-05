@extends('layouts.staff-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('staff.promissory-notes.index')}}">Promissory Notes</a></li>
        <li class="breadcrumb-item active">Active</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
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

            <div class="col-12 float-right text-white">
                @component('components.promissory-note-status', ['note'=> $promissoryNote])
                @endcomponent
            </div>

            <br><br>

            <div class="row">
                <div class="col-12 card-statistics">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h5>₦ {{number_format($promissoryNote->principal, 2)}}</h5>
                                        <i class="icon-wallet"></i>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <!--<p class="text-muted">Avg. Session</p>-->
                                        <p class="text-muted">Principal Amount</p>
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h5>₦ {{number_format($promissoryNote->maturity_value, 2)}}</h5>
                                        <i class="icon-wallet"></i>
                                    </div>
                                    <div class="d-flex justify-content-between">
                        
                                        <p class="text-muted">Maturity Value</p>
                                         <p class="text-muted">{{$promissoryNote->monthsLeft}} payment(s) left</p>
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h5>₦ {{number_format($promissoryNote->current_value, 2)}}</h5>
                                    
                                        <i class="icon-briefcase"></i>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted">Current Value</p>
                                        <!--<p class="text-muted">max: 143</p>-->
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h5>₦ {{number_format($promissoryNote->interest, 2)}}</h5>
                                    
                                        <i class="icon-briefcase"></i>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted">Expected Profit</p>
                                        <!--<p class="text-muted">max: 143</p>-->
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h5>₦ {{number_format($promissoryNote->payable_value, 2)}}</h5>
                                    
                                        <i class="icon-briefcase"></i>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted">Payable Value</p>
                                        <!--<p class="text-muted">max: 143</p>-->
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h5>₦ {{number_format($promissoryNote->tax_paid, 2)}}</h5>
                                    
                                        <i class="icon-briefcase"></i>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted">Tax Paid</p>
                                        <!--<p class="text-muted">max: 143</p>-->
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h5> {{date('d M Y', strtotime($promissoryNote->maturity_date))}}</h5>
                                    
                                        <i class="icon-briefcase"></i>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted">Expires</p>
                                        <!-- <p class="text-muted">{{$promissoryNote->tenure}} month(s)</p> -->
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-md-3 grid-margin stretch-card card-tile">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between pb-2">
                                        <h5> {{strtoupper($promissoryNote->interest_payment_cycle)}}</h5>
                                    
                                        <i class="icon-briefcase"></i>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <p class="text-muted">Setup Method</p>
                                       
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <br><br>


            <div class="row">
                <div class="col-12 card-statistics">
                    <div class="card">
                        <div class="card-header">
                            <a class="float-right" target="_blank" href="{{$promissoryNote->certificateUrl}}">View Certificate</a>
                            <h2 class="card-title"> Promissory Transaction Log</h2>
                        </div>
                        <div class="card-body">
                            
                            <table  id="order-listing" class="table table-responsive table-hover table-outline mb-0">

                                <thead class="thead-light">
                                    <tr>
                                        <th><i class="icon-credit-card"></i></th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Direction</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($promissoryNote->transactions as $transaction)
                                       
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{$transaction->amount}}</td>
                                            <td>{{$transaction->description}}</td>
                                            <td>{{$transaction->direction}}</td>
                                            <td>{{$transaction->date}}</td>
                                        </tr>
                                    @empty
                                        <p>No transaction on this promisorry note</p>
                                    @endforelse
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')

    <script src="{{asset('assets/js/data-table.js')}}">
    </script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.10.19/api/sum().js"></script>
   
@endsection