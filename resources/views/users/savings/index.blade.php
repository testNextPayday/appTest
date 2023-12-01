@extends('layouts.user')
@section('content')
<main class="main">
      <div class="container-fluid">
        <div class="animated fadeIn">
          
          <!--/.row-->
          <div class="row">
            <div class="col-sm-6 col-lg-6">
              <div class="card text-white bg-primary">
                <div class="card-body pb-0">
                  <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="icon-note"></i>
                  </button>
                  <h4 class="mb-0">{{Auth::user()->loanRequests->count()}}</h4>
                  <p><a style="color:white" href="{{route('users.loan-requests.index')}}">Loan Requests</a></p>
                </div>
                <div class="chart-wrapper px-3" style="height:70px;">
                  <canvas id="card-chart1" class="chart" height="70"></canvas>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-6">
              <div class="card text-white bg-info">
                <div class="card-body pb-0">
                  <button type="button" class="btn btn-transparent p-0 float-right">
                    <i class="icon-arrow-left-circle"></i>
                  </button>
                  <h4 class="mb-0">{{Auth::user()->receivedLoans->count()}}</h4>
                  <p><a style="color:white" href="{{route('users.loans.received')}}">Loans Received</a></p>
                </div>
                <div class="chart-wrapper px-3" style="height:70px;">
                  <canvas id="card-chart2" class="chart" height="70"></canvas>
                </div>
              </div>
            </div>
            @if (Auth::user()->virtualAccount)
            <div class="col-sm-6 col-lg-6">
              <div class="card text-white bg-success">
                <div class="card-header">Virtual Account</div>
                <div class="card-body">
                  A/c No: {{ Auth::user()->virtualAccount->number }} <hr class="bg-light">
                  Bank:{{Auth::user()->virtualAccount->bank}} <hr class="bg-light">
                  A/c Name: {{ Auth::user()->virtualAccount->name }}
                </div>
              </div>
            </div>
           @endif
          </div>
        </div>
      </div>
    <!-- /.conainer-fluid -->
  </main>
@endsection

@section('page-js')
<script src="{{asset('coreui/js/views/main.js')}}"></script>
<script type="text/javascript">
    $(window).on('load',function(){
        $('#npdmodal').modal('show');
    });
</script>
@endsection