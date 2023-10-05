@extends('layouts.admin-new')
@section('page-css')
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@endsection
@section('content')
<main class="main">
    <!-- Breadcrumb -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item"><a href="{{route('admin.affiliates.targets')}}">Targets</a></li>
        <li class="breadcrumb-item active">All</li>
    </ol>

    <div class="container-fluid">

        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <target-manager :is-admin="true"></target-manager>
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