@extends('layouts.users-offline')

@section('page-css')

    <style>

        body {

            font-size:large;
        }

        .sidebar-fixed .main, .sidebar-fixed .app-footer {
            margin-left: 50px;
        }

        .card {

            border:none;
            border-radius : 10px;
            padding: 60px 20px;
            color: grey;
        }

        .btn-rounded {
            border-radius: 10px;
            outline:none;
            padding:8px 10px;
        }

    </style>

@endsection
@section('content')

    
    <main class="main">
    
        <div class="container-fluid">
        
            <div class="row mt-5">

                

                <div class="col-md-12">

                    <div class="card">

                        
                        <div class="card-body row">

                            <div class="col-md-4">

                                <img src="{{asset('images/setup.jpg')}}" class="img-fluid img-responsive img-rounded">

                            </div>


                            <div class="col-md-8">
                                @php($otpEnabled = config('remita.otp_activation_enabled'))
                                <user-loan-setup-form
                                :paykey="'{{config('paystack.publicKey')}}'"
                                :loan="{{$loan}}"
                                :user="{{$loan->user}}"
                                :banks="{{$loan->user->bankDetails}}"
                                :mandateurl='"{{$loan->mandateUrl}}"'
                                :otpenabled="{{json_encode($otpEnabled)}}">
                                </user-loan-setup-form>


                            </div>


                        </div>

                    </div>

                </div>


            </div>
        </div>

    </main>
@endsection