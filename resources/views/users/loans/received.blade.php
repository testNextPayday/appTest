@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Loans</a></li>
        <li class="breadcrumb-item active">Received</li>

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
                            Loans Received
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-hover table-outline mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center"><i class="icon-credit-card"></i></th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Interest %</th>
                                        <th class="text-center">Tenure</th>
                                        <th>Loan Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($loans as $loan)
                                    <tr>
                                        <td class="text-center">
                                            {{$loop->iteration}}
                                        </td>
                                        <td>
                                            <div>{{$loan->reference}}</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small text-muted">
                                               ₦ {{$loan->amount}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">
                                                {{$loan->interest_percentage}}
                                            </div>
                                        </td>
                                    
                                        <td class="text-center">
                                            <div>
                                                {{$loan->due_date->diffInMonths($loan->created_at)}} Months ({{$loan->due_date->diffForHumans()}})
                                            </div>
                                        </td>
                                        <td>
                                            @component('components.loan-status', ['loan' => $loan])
                                            @endcomponent
                                        </td>
                                        <td>
                                            <a href="{{route('users.loans.view', ['reference' => $loan->reference])}}" class="btn btn-primary">
                                                <i class="icon-eye"></i>
                                                View Loan
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            You have not received any loans yet
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
</main>
@endsection

@section('page-js')
@endsection