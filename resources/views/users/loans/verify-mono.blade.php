@extends('layouts.user')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="#">Verify Mono Status</a></li>
    </ol>

    <div class="container-fluid">
        <div class="animated fadeIn">            
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                        @include('flash-message')           
                        </div>             
                        <div class="card-body">

                        <div class="row">
                            <div class="col-md-6 offset-md-2">
                                <p>Please click the link below to verify Mono Setup Status </p>
                                <form action="{{route('users.mono.verify.status')}}" method="post">
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