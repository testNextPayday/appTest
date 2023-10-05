@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Loan Requests</a></li>
        <li class="breadcrumb-item active">New</li>

        <li class="breadcrumb-menu">
          <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-primary btn-lg waves-effect text-white" href="{{route('users.loan-requests.create')}}" style="border-radius: 20px;"> 
             <span style="font-size: 0.9rem;"> <i class="icon-cursor text-white"></i> Get Loan</span>
            </a>
          </div>
        </li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                @forelse($loanRequests as $loanRequest)
                <div class="col-6 col-lg-4">
                    <div class="card">
                        <div class="card-body p-3 clearfix">
                            <i class="fa fa-cogs bg-primary p-3 font-4xl mr-3 float-left"></i>
                            <div class="h5 text-primary mb-0 mt-2">₦ {{number_format($loanRequest->amount, 2)}}</div>
                            <div class="text-muted text-uppercase font-weight-bold font-xs">{{$loanRequest->reference}}</div>
                            <div class="text-muted text-uppercase font-xs">{{$loanRequest->funds()->count()}} Responses
                                &nbsp;
                                @if($loanRequest->status == 0)
                                <span class="badge badge-danger">Inactive</span>
                                @elseif($loanRequest->status == 1)
                                <span class="badge badge-warning">Pending</span>
                                @elseif($loanRequest->status == 2)
                                <span class="badge badge-success">Active</span>
                                @elseif($loanRequest->status == 3)
                                <span class="badge badge-danger">Cancelled</span>
                                @elseif($loanRequest->status == 4)
                                <span class="badge badge-success">Taken</span>
                                @elseif($loanRequest->status == 5)
                                <span class="badge badge-danger">Declined - Employer</span>
                                @elseif($loanRequest->status == 6)
                                <span class="badge badge-danger">Declined - Admin</span>
                                @elseif($loanRequest->status == 7)
                                <span class="badge badge-warning">Loan Request Referred</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer px-3 py-2">
                            <a class="font-weight-bold font-xs btn-block text-muted" href="{{route('users.loan-requests.view', $loanRequest->reference)}}">
                                View Request 
                            <i class="fa fa-angle-right float-right font-lg"></i></a>
                        </div>
                    </div>
                </div>
                <!--/.col-->
                @empty
                <div class="col-sm-12 text-center">You've not made any loan requests yet</div>
                @endforelse
          </div>
          <!--/.row-->
          
        </div>
    </div>
</main>
@endsection

@section('page-js')
  
@endsection