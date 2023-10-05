@extends('layouts.investor')
@section('content')
<main class="main">
  <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('users.loan-requests.index')}}">Promissory Note</a></li>
        <li class="breadcrumb-item active">Fund Account</li>     
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row justify-content-center">              
                <div class="col-sm-6">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <p>Please click the link below to verify Mono Payment </p>
                            <form action="{{route('promissory-note.verify.monostatus')}}" method="post">
                                @csrf
                                <input type="hidden" name="reference" id="" value="{{ Request::get('reference') }}">
                                <button class="btn btn-block btn-success btn-rounded">
                                    Verify Status
                                </button>
                            </form>      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
</main>
@endsection

@section('page-js')

<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> 
<script type="application/javascript" src="https://connect.withmono.com/connect.js"></script>


@endsection