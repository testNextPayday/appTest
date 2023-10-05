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
                                <br>
                                
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Setup Loan
                                @if(session()->has('okraMesg'))
                                    <div class="alert">
                                        <div class="alert alert-danger">
                                            {{ session()->get('okraMesg')}}
                                        </div>
                                    </div>
                                @elseif(session()->has('okraSuccessMesg'))
                                    <div class="alert">
                                        <div class="alert alert-success">
                                            {{ session()->get('okraSuccessMesg')}}
                                        </div>
                                    </div>
                                @endif

                            @php session()->forget('okraMesg') @endphp
                            @php session()->forget('okraSuccessMesg') @endphp
                        </div>
                        @inject('collectionDates', '\App\Okra\CollectionDates') 
                                           
                        <div class="card-body">
                           
                            @php($otpEnabled = config('remita.otp_activation_enabled'))
                            <user-loan-setup-form
                                :paykey="'{{config('paystack.publicKey')}}'"
                                :loan="{{$loan}}"
                                :user="{{$loan->user}}"
                                :banks="{{$loan->user->bankDetails}}"
                                :mandateurl="'{{$loan->mandateUrl}}'"
                                :otpenabled="{{json_encode($otpEnabled)}}"
                                :debitstartdate="'{{$collectionDates::getStartDateHtml()}}'"
                                :debitenddate="'{{$collectionDates::getEndDateHtml($loan)}}'">
                            </user-loan-setup-form>

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