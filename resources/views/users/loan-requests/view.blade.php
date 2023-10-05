@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('users.loan-requests.index')}}">Loan Requests</a></li>
        <li class="breadcrumb-item active">{{$loanRequest->reference}}</li>

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
                        <div class="card-header">
                            Loan Request Details
                            <span class="pull-right">
                                @component('components.lr-status', ['loanRequest' => $loanRequest])
                                @endcomponent
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="card-group mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-diamond"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($loanRequest->amount, 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Amount</small>
                                        <?php 
                                            $insurance = 2.5/100 * $loanRequest->amount;
                                        ?>
                                        <small class="text-muted text-uppercase font-weight-bold">INSURANCE: ₦{{number_format($insurance, 2)}}</small>
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-pie-chart"></i>
                                        </div>
                                        <div class="h4 mb-0">{{ (! $loanRequest->using_armotization) ? number_format($loanRequest->emi() + $loanRequest->managementFee(), 2) : number_format($loanRequest->emi(),2)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Monthly Repayment</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-speedometer"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loanRequest->duration}} Months</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Duration</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-layers"></i>
                                        </div>
                                        <div class="h4 mb-0">₦ {{number_format($loanRequest->funds()->sum('amount'), 0)}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Realized</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-compass"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loanRequest->percentage_left}} %</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Percentage Left</small>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="h1 text-muted text-right mb-4">
                                            <i class="icon-calendar"></i>
                                        </div>
                                        <div class="h4 mb-0">{{$loanRequest->expected_withdrawal_date->toDateString()}}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Date Expected</small>
                                    </div>
                                </div>
            
                            </div>
                            
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    @if($investor = $loanRequest->investor)
                                        <h6>ASSIGNED TO {{$investor->reference}}</h6>
                                    @endif
                                </div>
                                <div class="col-sm-6 text-right">
                                    @component('components.lr-action-button', ['loanRequest' => $loanRequest, 'prefix' => 'users'])
                                    @endcomponent
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/.col-->
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Funds for this Request ({{$loanRequest->funds()->count()}})
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-people"></i></th>
                                        <th>Funder</th>
                                        <th class="text-center">Offer</th>
                                        <th>Fund Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loanRequest->funds()->latest()->get() as $fund)
                                    <tr>
                                        <td class="text-center">
                                            <div class="avatar">
                                                <img src="{{$fund->investor->avatar}}" class="img-avatar" alt="avatar">
                                                <span class="avatar-status badge-success"></span>
                                             </div>
                                        </td>
                                        <td>
                                            <div>{{$fund->investor->reference}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                                ₦ {{$fund->amount}} <span>({{$fund->percentage}}%)</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$fund->created_at->toDateString()}}
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            You've not received funds for this Request
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
            <!--/.row-->
        </div>

    </div>
    <!-- /.conainer-fluid -->
    
    <div class="modal fade" id="assignRequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-primary" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign To An Investor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form method="post"
                    action="{{ route('users.loan-requests.assign-investor', ['loanRequest' => $loanRequest->reference])}}">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Enter Investor Unique ID</label>
                            <input type="text" name="reference" class="form-control"
                                required value="{{ old('reference') }}"
                                placeholder="Investor Unique ID"/>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign Request</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</main>
@endsection

@section('page-js')
@endsection