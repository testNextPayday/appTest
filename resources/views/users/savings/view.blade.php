@extends('layouts.user')
@section('content')
    <main class="main">
        <div class="container-fluid mt-5">
            <div class="animated fadeIn">

                <div class="row">
                    <div class="col-sm-4 col-lg-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body pb-0">
                                <button type="button" class="btn btn-transparent p-0 float-right">
                                    <i class="icon-note"></i>
                                </button>
                                <h2 class="mb-0">{{ number_format($savings->amount, 2) }}</h2>
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
                                <h2 class="mb-0">{{ number_format($savings->amount + $savings->interest, 2) }}</h2>
                                <p><a style="color:white" href="{{ route('users.loan-requests.index') }}">Total Interest on
                                        Savings</a></p>
                            </div>
                            <div class="chart-wrapper px-3" style="height:70px;">
                                <canvas id="card-chart2" class="chart" height="70"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Transaction log --}}

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Savings Log
                            </div>
                            <div class="card-body">
                                <table class="table table-responsive-sm table-hover table-outline mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>SN</th>
                                            <th>Amount</th>
                                            <th class="text-center">Dec</th>
                                            <th class="">Status</th>
                                            <th class="">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @forelse($savings_transactions as $transaction)
                                      <tr>
                                          <td class="text-center">
                                              {{$loop->iteration}}
                                          </td>
                                          
                                          <td>
                                              <div class="{{$transaction->type == 'credit' ? 'text-success' : 'text-danger'}}">₦ {{ number_format($transaction->amount, 2) }}</div>
                                          </td>
                                          
                                          <td class="text-center">
                                              <div class="small text-muted">
                                                  {{$transaction->description}}
                                              </div>
                                          </td>
                                          <td>
                                            <div>
                                                @if($transaction->status == 'success')
                                                    <span class="badge badge-success">{{ $transaction->status }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $transaction->status }}</span>
                                                @endif
                                            </div>
                                            </td>
                                      <td>{{ $transaction->created_at->format('d M, Y')}}</td>
                                      </tr>
                                      @empty
                                      <tr>
                                          <td colspan="5" class="text-center">
                                              You have not made any transaction yet
                                          </td>
                                      </tr>
                                      @endforelse 
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--/.col-->
                </div>
                
            </div>
        </div>
        <!-- /.conainer-fluid -->
    </main>
@endsection

@section('page-js')
    <script src="{{ asset('coreui/js/views/main.js') }}"></script>
    <script type="text/javascript">
        $(window).on('load', function() {
            $('#npdmodal').modal('show');
        });
    </script>
@endsection
