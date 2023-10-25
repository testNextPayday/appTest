@extends('layouts.user')
@section('content')
<main class="main">

      <!-- Breadcrumb -->
      <ol class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>

        <!-- Breadcrumb Menu-->
        <li class="breadcrumb-menu d-md-down-none">
          <div class="btn-group" role="group" aria-label="Button group">
            <a class="btn" href="./"><i class="icon-wallet"></i> &nbsp;Wallet Balance: ₦ {{number_format(Auth::user()->wallet, 0)}}</a>
            <a class="btn" href="#"><i class="icon-lock"></i> &nbsp;Escrow Balance: ₦ {{number_format(Auth::user()->escrow, 0)}}</a>
            <a class="btn" href="#"><i class="icon-lock"></i> &nbsp;Loan Balance: ₦ {{number_format(Auth::user()->masked_loan_wallet, 0)}}</a>
            <a class="btn btn-default" href="#" data-toggle="modal" data-target="#fundWallet"><i class="icon-arrow-up"></i> Fund Wallet</a>
          </div>
        </li>

        <li class="breadcrumb-menu">
          <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-primary btn-lg waves-effect text-white" href="{{route('users.loan-requests.create')}}" style="border-radius: 20px;"> 
             <span style="font-size: 0.9rem;"> <i class="icon-cursor text-white"></i> Get Loan</span>
            </a>


            @if (!Auth::user()->virtualAccount)
      <form action="{{ route('create_virtual_account') }}" method="post">
        @csrf
       
        <button class="btn btn-md btn-success text-light ml-2 btn-lg waves-effect rounded-pill"> &nbsp;
          <small><i class="fa fa-money"></i>
            Create Virtual Account</button></small>
    </form>
     @endif

     
          </div>
        </li>
      </ol>

      <div class="container-fluid">

        <div class="animated fadeIn">
          
          <div class="row">

            <div class="col-sm-12 col-lg-4">
              <div class="card">
                <div class="card-body p-0 clearfix">
                  <i class="icon-wallet bg-primary p-4 px-5 font-2xl mr-3 float-left"></i>
                  <div class="h5 text-primary mb-0 pt-3">₦ {{number_format(Auth::user()->wallet, 0)}}</div>
                  <div class="text-muted text-uppercase font-weight-bold font-xs">Wallet Balance</div>
                </div>
              </div>
            </div>
            <!--/.col-->

            <div class="col-sm-12 col-lg-4">
              <div class="card">
                <div class="card-body p-0 clearfix">
                  <i class="icon-lock bg-info p-4 px-5 font-2xl mr-3 float-left"></i>
                  <div class="h5 text-info mb-0 pt-3">₦ {{number_format(Auth::user()->escrow, 0)}}</div>
                  <div class="text-muted text-uppercase font-weight-bold font-xs">Escrow Balance</div>
                </div>
              </div>
            </div>
            <!--/.col-->

            <div class="col-sm-12 col-lg-4">
              <div class="card">
                <div class="card-body p-0 clearfix">
                  <i class="icon-lock bg-info p-4 px-5 font-2xl mr-3 float-left"></i>
                  <div class="h5 text-info mb-0 pt-3">₦ {{number_format(Auth::user()->loan_wallet, 0) }}</div>
                  <div class="text-muted text-uppercase font-weight-bold font-xs">Loan Balance</div>
                </div>
              </div>
            </div>

            <div class="col-sm-12 col-lg-4" data-toggle="modal" data-target="#fundWallet">
              <div class="card" style="cursor:pointer">
                <div class="card-body p-0 clearfix">
                  <i class="icon-diamond bg-warning p-4 px-5 font-2xl mr-3 float-left"></i>
                  <div class="h5 text-warning mb-0 pt-3 text-center"><i class="icon-arrow-up"></i></div>
                  <div class="text-muted text-uppercase font-weight-bold font-xs text-center">Fund your Wallet</div>
                </div>
              </div>
            </div>
            <!--/.col-->
          </div>
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

  <!-- Modal -->
  <div class="modal fade" id="npdmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="text-muted" id="exampleModalCenterTitle">Book New Loan</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="conainer-fluid">
            <div class="row">
              <div class="col-md-7">
                <img src="/images/popup2.svg">
              </div>
               <div class="col-md-5">
                <h3>Get Started</h3>
                <p>And make a loan request without collateral </p>
                <a href="{{ route('users.loan-requests.create')}}">
                  <button type="button" class="btn btn-pill btn-outline-primary">Book Now</button>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- <div class="d-none">
    @isset(Auth::user()->repaymentPlans->sum('emi_penalties'))
      {{  }}
    @endisset
  </div> --}}
@endsection

@section('page-js')
<script src="{{asset('coreui/js/views/main.js')}}"></script>
<script type="text/javascript">
    $(window).on('load',function(){
        $('#npdmodal').modal('show');
    });
</script>
@endsection