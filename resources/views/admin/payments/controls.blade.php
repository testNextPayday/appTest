@extends('layouts.admin-new')

@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.payment.controls')}}">Control Transfers</a></li>
        <li class="breadcrumb-item active">All</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            
            <div class="row">
            
                <div class="col-md-12">

                    <div class="card">

                        <div class="card-header"></div>

                        <div class="card-body">
                        
                            <transfer-controls></transfer-controls>

                        </div>
                        
                    </div>

                </div>

            </div>

            <div class="row">
            
                <div class="col-md-12">

                    <div class="card">

                        <div class="card-header"></div>

                        <div class="card-body">
                        
                            <retry-statements :requests="{{$requests}}"></retry-statements>

                        </div>
                        
                    </div>

                </div>

            </div>
            
           
            <!--/.row-->
        </div>

        
    </div>
    <!-- /.conainer-fluid -->
</main>
@endsection

@section('page-js')
@endsection